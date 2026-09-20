<?php
declare(strict_types=1);

final class AdminTemplates
{
    public static function index(): void
    {
        Auth::requirePerm('templates.view');
        Templates::ensureStarters();
        $pages = Templates::allPages();
        foreach ($pages as &$p) {
            $p['section_count'] = Templates::sectionCount($p['sections_json'] ?? '[]');
            $p['usage'] = Templates::usage((int) $p['id']);
        }
        unset($p);
        View::admin('templates/index', [
            'title' => 'Templates',
            'pages' => $pages,
            'sections' => Database::all('SELECT * FROM section_templates ORDER BY name'),
            'postTypes' => Cpt::activeTypes(),
        ]);
    }

    public static function pageNew(): void
    {
        Auth::requirePerm('templates.create');
        Templates::ensureStarters();
        $forType = Request::int('for_type');
        $type = $forType ? Cpt::typeById($forType) : null;
        $id = Templates::createBlank(
            $type ? $type['singular_name'] . ' Template' : 'Untitled page template',
            $type && (int) $type['has_archive'] ? 'landing' : 'standard',
            $type ? (int) $type['id'] : null
        );
        Audit::log('page_template.created', 'page_template', $id, $type ? ['for_post_type' => $type['slug']] : []);
        if ($type) {
            View::flash('success', 'Blank template created and set as the default for ' . $type['name'] . '. Add your sections below.');
        }
        View::redirect('/admin/templates/pages/' . $id . '/');
    }

    public static function pageEdit(string $id): void
    {
        Auth::requirePerm('templates.edit');
        $row = Database::one('SELECT * FROM page_templates WHERE id = ?', [(int) $id]);
        if (!$row) {
            View::admin('errors/404', ['title' => 'Not found']);
            return;
        }
        View::admin('templates/page-form', [
            'title' => 'Edit template: ' . $row['name'],
            'row' => $row,
            'sections' => json_decode($row['sections_json'] ?: '[]', true) ?: [],
            'registry' => SectionRegistry::all(),
            'usage' => Templates::usage((int) $row['id']),
            'sectionTemplates' => Database::all('SELECT * FROM section_templates ORDER BY name'),
        ]);
    }

    public static function pageSave(string $id): void
    {
        Auth::requirePerm('templates.edit');
        $row = Database::one('SELECT * FROM page_templates WHERE id = ?', [(int) $id]);
        if (!$row) {
            View::redirect('/admin/templates/');
        }
        $existing = json_decode($row['sections_json'] ?: '[]', true) ?: [];
        $sections = Templates::packFromPost($existing);
        $action = Request::str('action');

        if ($action === 'add_section') {
            $type = Request::str('add_type') ?: 'page_hero';
            if (isset(SectionRegistry::all()[$type])) {
                $sections[] = ['type' => $type, 'content' => SectionRegistry::defaults($type)];
            }
        } elseif ($action === 'add_block') {
            $st = Database::one('SELECT * FROM section_templates WHERE id = ?', [Request::int('section_template_id')]);
            if ($st && isset(SectionRegistry::all()[$st['type']])) {
                $sections[] = [
                    'type' => $st['type'],
                    'content' => json_decode($st['content_json'] ?: '{}', true) ?: SectionRegistry::defaults($st['type']),
                ];
            }
        } elseif ($action === 'delete_section') {
            $rm = Request::int('action_idx');
            if (isset($sections[$rm])) {
                array_splice($sections, $rm, 1);
            }
        } elseif ($action === 'duplicate_section') {
            $dup = Request::int('action_idx');
            if (isset($sections[$dup])) {
                array_splice($sections, $dup + 1, 0, [$sections[$dup]]);
            }
        } elseif ($action === 'move_up' || $action === 'move_down') {
            $from = Request::int('action_idx');
            $to = $action === 'move_up' ? $from - 1 : $from + 1;
            if (isset($sections[$from], $sections[$to])) {
                [$sections[$from], $sections[$to]] = [$sections[$to], $sections[$from]];
            }
        }

        $name = Request::str('name') ?: $row['name'];
        Database::update('page_templates', [
            'name' => $name,
            'description' => Request::str('description'),
            'page_type' => Request::str('page_type') ?: 'standard',
            'sections_json' => Html::json(array_values($sections)),
        ], 'id = ?', [(int) $id]);
        Audit::log('page_template.updated', 'page_template', (int) $id);

        if ($action === 'save_close') {
            View::flash('success', 'Page template saved.');
            View::redirect('/admin/templates/');
        }
        View::flash('success', match ($action) {
            'add_section', 'add_block' => 'Section added.',
            'delete_section' => 'Section removed.',
            'duplicate_section' => 'Section duplicated.',
            'move_up', 'move_down' => 'Section order updated.',
            default => 'Page template saved.',
        });
        View::redirect('/admin/templates/pages/' . (int) $id . '/');
    }

    public static function pageDuplicate(): void
    {
        Auth::requirePerm('templates.create');
        $src = Database::one('SELECT * FROM page_templates WHERE id = ?', [Request::int('id')]);
        if (!$src) {
            View::redirect('/admin/templates/');
        }
        $id = Database::insert('page_templates', [
            'slug' => Slug::uniqueInTable('page_templates', $src['slug'] . '-copy'),
            'name' => $src['name'] . ' (copy)',
            'description' => $src['description'],
            'page_type' => $src['page_type'],
            'sections_json' => $src['sections_json'],
        ]);
        Audit::log('page_template.duplicated', 'page_template', $id);
        View::flash('success', 'Template duplicated. You can rename and edit it.');
        View::redirect('/admin/templates/pages/' . $id . '/');
    }

    public static function pageDelete(): void
    {
        Auth::requirePerm('templates.delete');
        $id = Request::int('id');
        $row = Templates::page($id);
        if (!$row) {
            View::flash('error', 'Template not found.');
            View::redirect('/admin/templates/');
        }
        $usage = Templates::usage($id);
        if ($usage['post_types']) {
            $names = implode(', ', array_column($usage['post_types'], 'name'));
            View::flash('error', '“' . $row['name'] . '” is the default template for ' . $names . '. Point those post types at another template first.');
            View::redirect('/admin/templates/');
        }
        Database::delete('page_templates', 'id = ?', [$id]);
        Audit::log('page_template.deleted', 'page_template', $id, ['name' => $row['name']]);
        View::flash('success', 'Page template deleted.');
        View::redirect('/admin/templates/');
    }

    public static function sectionNew(): void
    {
        Auth::requirePerm('templates.create');
        $type = Request::str('type') ?: 'hero';
        if (!isset(SectionRegistry::all()[$type])) {
            $type = 'hero';
        }
        View::admin('templates/section-form', [
            'title' => 'New section template',
            'row' => null,
            'type' => $type,
            'content' => SectionRegistry::defaults($type),
            'registry' => SectionRegistry::all(),
        ]);
    }

    public static function sectionEdit(string $id): void
    {
        Auth::requirePerm('templates.edit');
        $row = Database::one('SELECT * FROM section_templates WHERE id = ?', [(int) $id]);
        if (!$row) {
            View::admin('errors/404', ['title' => 'Not found']);
            return;
        }
        View::admin('templates/section-form', [
            'title' => 'Edit section template',
            'row' => $row,
            'type' => $row['type'],
            'content' => json_decode($row['content_json'] ?: '{}', true) ?: [],
            'registry' => SectionRegistry::all(),
        ]);
    }

    public static function sectionSave(): void
    {
        $id = Request::int('id');
        Auth::requirePerm($id ? 'templates.edit' : 'templates.create');
        $type = Request::str('type') ?: 'hero';
        if (!isset(SectionRegistry::all()[$type])) {
            View::flash('error', 'Unknown block type.');
            View::redirect('/admin/templates/');
        }
        $content = SectionRegistry::defaults($type);
        foreach (SectionRegistry::fields($type) as $f) {
            $key = 'f_' . $f['k'];
            if ($f['t'] === 'html') {
                $content[$f['k']] = Html::allowedHtml((string) ($_POST[$key] ?? ''));
            } else {
                $content[$f['k']] = trim((string) ($_POST[$key] ?? ''));
            }
        }
        $data = [
            'name' => Request::str('name') ?: SectionRegistry::label($type),
            'type' => $type,
            'content_json' => Html::json($content),
            'created_by' => Auth::id(),
        ];
        if ($id) {
            unset($data['created_by']);
            Database::update('section_templates', $data, 'id = ?', [$id]);
        } else {
            $id = Database::insert('section_templates', $data);
        }
        Audit::log('section_template.saved', 'section_template', $id);
        View::flash('success', 'Section template saved.');
        View::redirect('/admin/templates/');
    }

    public static function sectionDuplicate(): void
    {
        Auth::requirePerm('templates.create');
        $src = Database::one('SELECT * FROM section_templates WHERE id = ?', [Request::int('id')]);
        if (!$src) {
            View::redirect('/admin/templates/');
        }
        $id = Database::insert('section_templates', [
            'name' => $src['name'] . ' (copy)',
            'type' => $src['type'],
            'content_json' => $src['content_json'],
            'created_by' => Auth::id(),
        ]);
        Audit::log('section_template.duplicated', 'section_template', $id);
        View::flash('success', 'Section template duplicated.');
        View::redirect('/admin/templates/sections/' . $id . '/');
    }

    public static function sectionDelete(): void
    {
        Auth::requirePerm('templates.delete');
        Database::delete('section_templates', 'id = ?', [Request::int('id')]);
        View::flash('success', 'Section template deleted.');
        View::redirect('/admin/templates/');
    }
}
