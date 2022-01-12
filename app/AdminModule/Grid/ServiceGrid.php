<?php declare(strict_types=1);

namespace App\AdminModule\Grid;

use App\Model\ServiceModel;
use K2D\Core\AdminModule\Grid\BaseV2Grid;

use K2D\Core\Models\ConfigurationModel;
use K2D\Gallery\Models\GalleryModel;
use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Container;

class ServiceGrid extends BaseV2Grid
{


	/** @var ServiceModel */
	private ServiceModel $serviceModel;

	public ConfigurationModel $configuration;

	public function __construct(ServiceModel $serviceModel)
	{
		parent::__construct();
		$this->serviceModel = $serviceModel;
	}

	protected function build(): void
	{
		$this->model = $this->serviceModel;

		parent::build();

		$this->setDefaultOrderBy('created', true);
		$this->setFilterFactory([$this, 'gridFilterFactory']);

		$this->addColumn('name', 'Name');

		if ($this->presenter->configuration->getLanguagesCount() > 1) {
			$this->addColumn('lang', 'Language')->setSortable();
		}

		$this->addColumn('gallery', 'Gallery');
		$this->addColumn('public', 'Public');
		$this->addColumn('updated', 'Last updated')->setSortable();
		$this->addColumn('created', 'Created')->setSortable();

		$this->addRowAction('edit', 'Edit', static function (): void {});
		$this->addRowAction('delete', 'Delete', static function (ActiveRow $record): void {
			if ($record->cover) {
				unlink(WWW . '/upload/services/' . $record->id . '/' . $record->cover);
			}

			$record->delete();
		})
			->setProtected(false)
			->setConfirmation('Are you sure you want to delete this service?');
	}

	public function gridFilterFactory(Container $c): void
	{
		$c->addText('name', 'Service name')->setHtmlAttribute('placeholder', 'Filter by service name');
		$c->addText('lang', 'Service language')->setHtmlAttribute('placeholder', 'Filter by service language');
	}
}
