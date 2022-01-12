<?php declare(strict_types=1);

namespace App\AdminModule\Grid;

use App\Model\CategoryModel;
use App\Model\ArticleModel;
use K2D\Core\AdminModule\Grid\BaseV2Grid;
use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Container;

class ArticleGrid extends BaseV2Grid
{

	/** @var ArticleModel */
	private $articleModel;

	/** @var CategoryModel */
	private $articleCategoryModel;

	public function __construct(ArticleModel $articleModel, CategoryModel $articleCategoryModel)
	{
		parent::__construct();
		$this->articleModel = $articleModel;
		$this->articleCategoryModel = $articleCategoryModel;
	}

	protected function build(): void
	{
		$this->model = $this->articleModel;

		parent::build();

		$this->setDefaultOrderBy('created', true);
		$this->setFilterFactory([$this, 'gridFilterFactory']);

		$this->addColumn('title', 'Nadpis');
		$this->addColumn('category_id', 'Kategorie');
		$this->addColumn('perex', 'Perex');
		$this->addColumn('lang', 'Jazyk');
		$this->addColumn('created', 'Vytvořeno')->setSortable();
		$this->addColumn('public', 'Veřejná');
		$this->addColumn('gallery_id', 'Galerie');
		$this->addColumn('folder_id', 'Složka souborů');

		$this->addRowAction('edit', 'Upravit', static function () {});
		$this->addRowAction('delete', 'Smazat', static function (ActiveRow $record) {
			if ($record->cover) {
				unlink(WWW . '/upload/articles/' . $record->id . '/' . $record->cover);
			}

			$record->delete();
		})
			->setProtected(false)
			->setConfirmation('Opravdu chcete článek smazat?');
	}

	public function gridFilterFactory(Container $c): void
	{
		$c->addText('title');
		$c->addSelect('category_id')
			->setPrompt('---')
			->setItems($this->articleCategoryModel->getForSelect());
		$c->addSelect('public')
			->setPrompt('---')
			->setItems([0 => 'Ne', 1 => 'Ano']);
	}

	public function processFilters(Nette\Database\Table\Selection $data, array $filters): void
	{
		foreach ($filters as $column => $value) {
			if ($column === 'category_id') {
				$data->where($column, $value);
			}
		}

		unset($filters['category_id']);
		parent::processFilters($data, $filters);
	}

}
