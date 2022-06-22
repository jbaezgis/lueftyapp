<?php namespace App\Repositories;

use App\Models\Page;
use App\Models\PageMeta;
use App\Repositories\Repository;
use DB;
use Cache;

class PageRepository implements Repository{
	public $site = null;

	public function getPageBySlug($slug){
		$site = str_replace('.com', '', config('Site')->domain);
        $page = Page::select('ID', 'post_title', 'post_content', 'post_status', 'post_type', 'post_name')
		    ->where('post_type', $site)
			->where('post_status', 'publish')
		    ->where('post_name', $slug)
		    ->first();
		return  $page;
	}

	public function getRestPageBySlug($slug){
		$output = Cache::rememberForever('page-'.$slug, function() use ($slug){
			$lang = \App::getLocale();
			$output = new \stdClass();
			$output->metaDescription = '';
			$output->metaKeywords = '';
			$output->title = '';
			$output->content = '';

			//$content = file_get_contents(env('WP_BLOG_URL').'/wp-json/wp/v2/dominicanshuttles?slug='.$slug.'&lang='.$lang);
			//$content = json_decode($content);
			$content = [];
			if (count($content) > 0) {
				$page = $content[0];
				$output->title = isset($page->metadata->title) ? $page->metadata->title[0] : $page->title->rendered;
				$output->content = $page->content->rendered;
				$output->metaKeywords = $page->metadata->meta_keywords[0];
				$output->metaDescription = $page->metadata->meta_description[0];
			}
			return $output;
		});
		
		return $output;
	}

	public function getMetas($pageID){
		$output = Cache::rememberForever('metas-'.$slug, function() use ($slug){
			$output = new \stdClass;
			$output->metaDescription = '';
			$output->metaKeywords = '';

			$pageMeta = PageMeta::where('post_id', $pageID)->get();
			if ($pageMeta) {
				if ($pageMeta->where('meta_key', 'meta_description')->first()) {
					$output->metaDescription = $pageMeta->where('meta_key', 'meta_description')->first()->meta_value;
				}
				
				if ($pageMeta->where('meta_key', 'meta_keywords')->first()) {
					$output->metaKeywords = $pageMeta->where('meta_key', 'meta_keywords')->first()->meta_value;
				}
			}

			return $output;
		});

		return $output;
	}

	public function getAll(){}
	public function getById($id){}
	public function update($object, $data){}
	public function save($data){}
}
