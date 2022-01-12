<?php declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Model\ArticleModel;
use App\Model\CategoryModel;
use K2D\Core\Presenter\BasePresenter;
use K2D\File\Model\FileModel;
use K2D\File\Model\FolderModel;
use K2D\Gallery\Models\ImageModel;
use Nette\Utils\Paginator;

class SectionPresenter extends BasePresenter
{
	/** @var ArticleModel */
	private ArticleModel $articleModel;

	/** @var CategoryModel */
	private CategoryModel $categoryModel;

	/** @var ImageModel */
	private ImageModel $imageModel;

	/** @var FolderModel  */
	private FolderModel $folderModel;

	/** @var FileModel  */
	private FileModel $fileModel;

	public function __construct(ArticleModel $articleModel, CategoryModel $categoryModel, ImageModel $imageModel, FolderModel $folderModel, FileModel $fileModel)
	{
		parent::__construct();

		$this->articleModel = $articleModel;
		$this->categoryModel = $categoryModel;
		$this->imageModel = $imageModel;
		$this->folderModel = $folderModel;
		$this->fileModel = $fileModel;
	}

	public function renderDefault($section, int $page = 1): void
	{
		$vars = $this->configuration;
		$itemsPerPage = (int) $vars->itemsPerPage;

		$category = $this->categoryModel->getCategoryBySlug($section);
		$this->template->sectionCategory = $category;

		$articlesCount = $this->articleModel->getArticlesByCategoryCount($category->id);

		$paginator = new Paginator;
		$paginator->setPage($page); // číslo aktuální stránky
		$paginator->setItemsPerPage($itemsPerPage); // počet položek na stránce
		$paginator->setItemCount($articlesCount); // celkový počet položek, je-li znám

		$this->template->articles = $this->articleModel->getArticlesByCategory($category->id)->limit($paginator->getLength(), $paginator->getOffset());
		$this->template->paginator = $paginator;
		$this->template->categories = $this->categoryModel->getCategories();
	}

	public function renderArticle($section, $slug): void
	{
		$article = $this->articleModel->getArticle($slug);

		if (isset($article->perex))
			$this->template->perex = strip_tags($article->perex);

		$this->template->article = $article;

		$this->template->article_category = $this->categoryModel->getCategoryById($article->category_id);

		if ($article->gallery_id != NULL)
			$this->template->images = $this->imageModel->getImagesByGallery($article->gallery_id);

		if ($article->folder_id != NULL) {
			$this->template->folder = $this->folderModel->getFolder($article->folder_id);
			$this->template->files = $this->fileModel->getFilesByFolderId($article->folder_id);
			$this->template->filetypes = ['doc', 'docx', 'jpeg', 'jpg', 'pdf', 'png', 'ppt', 'pptx', 'txt', 'xls', 'xlsx'];
		}

		$url = $this->getHttpRequest()->getUrl()->getAbsoluteUrl();
		$this->template->url = $url;

		// get 4 other articles except currently read one
		$this->template->otherLatestArticles = $this->articleModel->getLatestArticlesExceptThisOne($slug);
	}
}
