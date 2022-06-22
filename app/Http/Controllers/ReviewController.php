<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\PageRepository;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller {

	public $pageRepo;

	public function __construct(PageRepository $pageRepository){
		parent::__construct();
		$this->pageRepo = $pageRepository;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$page = $this->pageRepo->getRestPageBySlug('customers-review');
		$reviews = Review::where('approved', 1)->orderBy('created_at', 'DESC')->paginate(10);
		$options = [
			'Unnaceptable',
			'Very Poor',
			'Poor',
			'Average',
			'Above Average',
			'Good',
			'Very Good',
			'Excellent'
		];

		return view('reviews.index', [
			'reviews' => $reviews,
			'options' => $options,
			'page' => $page,
			'metaDescription' => $page->metaDescription,
			'metaKeywords' => $page->metaKeywords
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'author'	=> 'required|min:2|max:80',
			'email'	=> 'required|email',
			'short_description' => 'required|max:250',
			'rate' => 'required',
			'reuse' => 'required',
			// 'g-recaptcha-response' => 'required|captcha'
		]);

		$input = $request->all();
		unset($input['g-recaptcha-response']);
		Review::create($input);

		return redirect()->back()->with('info', 'Your review has been submited!');
	}

}
