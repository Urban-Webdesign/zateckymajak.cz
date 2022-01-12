<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Model\ArticleModel;
use App\Model\CategoryModel;
use K2D\Box\Component\BoxComponent\BoxComponent;
use K2D\Box\Component\BoxComponent\BoxComponentFactory;
use K2D\Core\Presenter\BasePresenter;

class HomepagePresenter extends BasePresenter
{

	/** @inject */
	public BoxComponentFactory $boxFactory;

	/** @var ArticleModel */
	private ArticleModel $articleModel;

	/** @var CategoryModel */
	private CategoryModel $categoryModel;

	public function __construct(ArticleModel $articleModel, CategoryModel $categoryModel)
	{
		parent::__construct();
		$this->articleModel = $articleModel;
		$this->categoryModel = $categoryModel;
	}


	protected function createComponentBox(): BoxComponent
	{
		return $this->boxFactory->create();
	}

	public function renderDefault(): void
	{
		// get all articles
		$articles = $this->articleModel->getArticles();

		// get categories
		$this->template->categories = $this->categoryModel->getCategories();

		// get latest article
		$this->template->latestArticle = $this->articleModel->getLatestArticle();

		// get 4 other articles
		$this->template->otherLatestArticles = $articles->limit(3, 1);

		// get 3 latest from zpravodajstvi
		$this->template->zpravodajstviArticles = $this->articleModel->getArticlesByCategory(2)->limit(3, 4);

		// get 3 latest from sluzby a spolky
		$this->template->sluzbyArticles = $this->articleModel->getArticlesByCategory(3)->limit(3, 4);

		// get 3 latest from kultura a historie
		$this->template->kulturaArticles = $this->articleModel->getArticlesByCategory(4)->limit(3, 4);

		// get 3 latest from sport
		$this->template->sportArticles = $this->articleModel->getArticlesByCategory(5)->limit(3, 4);

		// get 3 latest from vzdelavani
		$this->template->vzdelavaniArticles = $this->articleModel->getArticlesByCategory(6)->limit(3, 4);

		// get 3 latest from blogy
		$this->template->blogyArticles = $this->articleModel->getArticlesByCategory(7)->limit(3, 4);
	}

	public function renderSections(): void
	{
		$this->template->categories = $this->categoryModel->getCategories();
	}

	public function renderAuthors(): void
	{

	}

	public function renderDocuments(): void
	{
		$this->template->folder_zpravodajstvi = $this->repository->getFilesByFolderId(5);
		$this->template->folder_sluzby = $this->repository->getFilesByFolderId(6);
		$this->template->folder_kultura = $this->repository->getFilesByFolderId(7);
		$this->template->folder_sport = $this->repository->getFilesByFolderId(8);
		$this->template->folder_vzdelavani = $this->repository->getFilesByFolderId(9);
		$this->template->folder_blogy = $this->repository->getFilesByFolderId(10);
		$this->template->filetypes = ['doc', 'docx', 'jpeg', 'jpg', 'pdf', 'png', 'ppt', 'pptx', 'txt', 'xls', 'xlsx'];
	}

	public function renderContact(): void
	{

	}

	public function renderSitemap(): void
	{

	}

}
