<?php

// Display
foreach (['title', 'meta', 'content', 'link'] as $key) {
    if (!$element["show_{$key}"]) { $props[$key] = ''; }
}

if (!$element['show_image']) { $props['image'] = $props['icon'] = ''; }

// Resets
if ($props['icon'] && !$props['image']) { $element['slider_panel_card_image'] = ''; }

// New logic shortcuts
$element['has_panel_card_image'] = $props['image'] && $element['slider_panel_card_image'] && $element['image_align'] != 'between';
$element['has_content_padding'] = $props['image'] && $element['slider_panel_content_padding'] && $element['image_align'] != 'between';

// If link is not set use the default image for the lightbox
if (!$props['link'] && $element['lightbox']) {
    $props['link'] = $props['image'];
}

// Image
$props['image'] = $this->render("{$__dir}/template-image", compact('props'));

if ($element['image_transition']) {

    $transition_toggle = $this->el('div', [
        'class' => [
            'uk-inline-clip [uk-transition-toggle {@image_link}]',
            'uk-border-{image_border}' => !$element['slider_panel_style'] || ($element['slider_panel_style'] && (!$element['slider_panel_card_image'] || $element['image_align'] == 'between')),
            'uk-box-shadow-{image_box_shadow} {@!slider_panel_style}',
            'uk-box-shadow-hover-{image_hover_box_shadow} {@!slider_panel_style}' => $props['link'] && ($element['image_link'] || $element['slider_panel_link']),
            'uk-margin[-{image_margin}]-top {@!image_margin: remove} {@!image_box_decoration}' => $element['image_align'] == 'between' || ($element['image_align'] == 'bottom' && !($element['slider_panel_style'] && $element['slider_panel_card_image'])),
        ],
    ]);
    $props['image'] = $transition_toggle($element, $props['image']);

}

if ($element['image_box_decoration']) {

    $decoration = $this->el('div', [

        'class' => [
            'uk-box-shadow-bottom {@image_box_decoration: shadow}',
            'tm-mask-default {@image_box_decoration: mask}',
            'tm-box-decoration-{image_box_decoration: default|primary|secondary}',
            'tm-box-decoration-inverse {@image_box_decoration_inverse} {@image_box_decoration: default|primary|secondary}',
            'uk-inline {@!image_box_decoration: |shadow}',
            'uk-margin[-{image_margin}]-top {@!image_margin: remove}' => $element['image_align'] == 'between' || ($element['image_align'] == 'bottom' && !($element['slider_panel_style'] && $element['slider_panel_card_image'])),
        ],

    ]);

    $props['image'] = $decoration($element, $props['image']);
}

// Panel/Card
$el = $this->el($props['link'] && $element['slider_panel_link'] ? 'a' : 'div', [

    'class' => [
        'el-item',
        'uk-margin-auto uk-width-{item_maxwidth}',
        'uk-panel {@!slider_panel_style}',
        'uk-card uk-{slider_panel_style} [uk-card-{slider_panel_size}]',
        'uk-card-hover {@!slider_panel_style: |card-hover} {@slider_panel_link}' => $props['link'],
        'uk-card-body {@slider_panel_style} {@!has_panel_card_image}',
        'uk-margin-remove-first-child' => (!$element['slider_panel_style'] && !$element['has_content_padding']) || ($element['slider_panel_style'] && !$element['has_panel_card_image']),
        'uk-flex {@slider_panel_style} {@has_panel_card_image} {@image_align: left|right}', // Let images cover the card height if the cards have different heights
        'uk-transition-toggle {@image_transition} {@slider_panel_link}' => $props['image'],
    ],

]);

// Image align
$grid = $this->el('div', [

    'class' => [
        'uk-child-width-expand',
        $element['slider_panel_style'] && $element['has_panel_card_image']
            ? 'uk-grid-collapse uk-grid-match'
            : ($element['image_grid_column_gap'] == $element['image_grid_row_gap']
                ? 'uk-grid-{image_grid_column_gap}'
                : '[uk-grid-column-{image_grid_column_gap}] [uk-grid-row-{image_grid_row_gap}]'),
        'uk-flex-middle {@image_vertical_align}',
    ],

    'uk-grid' => true,
]);

$cell_image = $this->el('div', [

    'class' => [
        'uk-width-{image_grid_width}[@{image_grid_breakpoint}]',
        'uk-flex-last[@{image_grid_breakpoint}] {@image_align: right}',
    ],

]);

// Content
$content = $this->el('div', [

    'class' => [
        'uk-card-body uk-margin-remove-first-child {@slider_panel_style} {@has_panel_card_image}',
        'uk-padding[-{!slider_panel_content_padding: |default}] uk-margin-remove-first-child {@!slider_panel_style} {@has_content_padding}',
        // 1 Column Content Width
        'uk-container uk-container-{slider_panel_content_width}' => $props['image'] && $element['image_align'] == 'top' && !$element['slider_panel_style'] && !$element['slider_panel_content_padding'] && !$element['item_maxwidth'] && (!$element['grid_default'] || $element['grid_default'] == '1') && (!$element['grid_small'] || $element['grid_small'] == '1') && (!$element['grid_medium'] || $element['grid_medium'] == '1') && (!$element['grid_large'] || $element['grid_large'] == '1') && (!$element['grid_xlarge'] || $element['grid_xlarge'] == '1'),
    ],

]);

$cell_content = $this->el('div', [

    'class' => [
        'uk-margin-remove-first-child' => (!$element['slider_panel_style'] && !$element['has_content_padding']) || ($element['slider_panel_style'] && !$element['has_panel_card_image']),

    ],

]);

// Link
$link = include "{$__dir}/template-link.php";

// Card media
if ($element['slider_panel_style'] && $element['has_panel_card_image']) {
    $props['image'] = $this->el('div', ['class' => [
        'uk-card-media-{image_align}',
        'uk-cover-container{@image_align: left|right}',
    ]], $props['image'])->render($element);
}

?>

<?= $el($element, $attrs) ?>

    <?php if ($props['image'] && in_array($element['image_align'], ['left', 'right'])) : ?>

        <?= $grid($element) ?>
            <?= $cell_image($element, $props['image']) ?>
            <?= $cell_content($element) ?>

                <?php if ($this->expr($content->attrs['class'], $element)) : ?>
                    <?= $content($element, $this->render("{$__dir}/template-content", compact('props', 'link'))) ?>
                <?php else : ?>
                    <?= $this->render("{$__dir}/template-content", compact('props', 'link')) ?>
                <?php endif ?>

            <?= $cell_content->end() ?>
        </div>

    <?php else : ?>

        <?php if ($element['image_align'] == 'top') : ?>
        <?= $props['image'] ?>
        <?php endif ?>

        <?php if ($this->expr($content->attrs['class'], $element)) : ?>
            <?= $content($element, $this->render("{$__dir}/template-content", compact('props', 'link'))) ?>
        <?php else : ?>
            <?= $this->render("{$__dir}/template-content", compact('props', 'link')) ?>
        <?php endif ?>

        <?php if ($element['image_align'] == 'bottom') : ?>
        <?= $props['image'] ?>
        <?php endif ?>

    <?php endif ?>

<?= $el->end() ?>
