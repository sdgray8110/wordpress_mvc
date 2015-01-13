<?php
$slug = archive_page_slug();
$page = new PageHelper($slug, 'archive');
get_header();

$page->content();

get_footer();