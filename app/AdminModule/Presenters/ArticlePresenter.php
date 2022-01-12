<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Grid\ArticleGridFactory;
use App\AdminModule\Grid\ArticleGrid;
use App\Model\CategoryModel;
use App\Model\ArticleModel;
use K2D\Core\Helper\Helper;
use K2D\Core\AdminModule\Presenter\BasePresenter;
use K2D\File\Model\FolderModel;
use K2D\Gallery\Models\GalleryModel;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Http\FileUpload;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

class ArticlePresenter extends BasePresenter
{

	/** @var ArticleModel */
	private $articles;

	/** @var CategoryModel */
	private $categories;

	/** @var GalleryModel */
	private $galleries;

	/** @var FolderModel */
	private $folders;

	/** @var ArticleGridFactory */
	private $articleGridFactory;

	public function __construct(ArticleModel $articleModel, CategoryModel $categoryModel, GalleryModel $galleryModel, FolderModel $folderModel, ArticleGridFactory $articleGridFactory)
	{
		parent::__construct();
		$this->articles = $articleModel;
		$this->categories = $categoryModel;
		$this->galleries = $galleryModel;
		$this->folders = $folderModel;
		$this->articleGridFactory = $articleGridFactory;
	}

	public function renderEdit($id = null): void
	{
		/** @var ActiveRow $row */
		if ($id !== null && $row = $this->articles->getTable()->get($id)) {

			$row = $row->toArray();

			/** @var DateTime $date */
			$date = $row['created'];
			$row['created'] = $date->format('j.n.Y');

			/** @var Form $form */
			$form = $this['editForm'];
			$form->setDefaults($row);

			$this->template->article = $row;
		} else {
			$this->template->article = null;
		}
	}

	public function createComponentEditForm(): Form
	{
		$form = new Form;

		$form->addHidden('id');

		$form->addText('title', 'Nadpis novinky')
			->addRule(Form::MAX_LENGTH, 'Maximálné délka je %s znaků', 255)
			->setRequired('Musíte uvést nadpis novinky');

		$form->addText('created', 'Datum')
			->setDefaultValue((new DateTime())->format('j.n.Y'))
			->setRequired('Musíte uvést datum novinky');

		$form->addSelect('category_id', 'Kategorie')
			->setItems($this->categories->getForSelect());

		$form->addSelect('lang', 'Jazyk')
			->setItems($this->configuration->languages, false);

		$form->addCheckbox('public', 'Veřejný článek')
			->setDefaultValue(true);

		$form->addUpload('image', 'Nahrát obrázek novinky');

		$form->addSelect('gallery_id', 'Připojit galerii')
			->setPrompt('Žádná')
			->setItems($this->galleries->getForSelect());

		$form->addSelect('folder_id', 'Připojit složku souborů')
			->setPrompt('Žádná')
			->setItems($this->folders->getForSelect());

		$form->addTextArea('perex', 'Perex', 100, 5)
			->setHtmlAttribute('class', 'form-wysiwyg');

		$form->addTextArea('content', 'Obsah', 100, 25)
			->setHtmlAttribute('class', 'form-wysiwyg');

		$form->addSubmit('save', 'Uložit');

		$form->onSubmit[] = function (Form $form) {
			$values = $form->getValues(true);
			$values['created'] = date_create_from_format('j.n.Y', $values['created'])->setTime(0, 0, 0);
			$values['slug'] = Strings::webalize($values['title']);

			/** @var FileUpload $file */
			$file = $values['image'];
			unset($values['image']);

			if ($values['id'] === '') {
				unset($values['id']);
				$values['id'] = $this->articles->insert($values)->id;
				$this->flashMessage('Článek vytvořen', 'success');
			} else {
				$this->articles->get($values['id'])->update($values);
				$this->flashMessage('Článek upraven', 'success');
			}

			$this->articles->get($values['id'])->update(['slug' => $values['id'] . '-' . $values['slug']]);

			$link = WWW . '/upload/articles/' . $values['id'] . '/';

			if (Helpers::isValidImage($file)) {
				$name = 'image.' . Helpers::getFileType($file);

				if (!file_exists($link)) {
					Helpers::mkdir($link);
				}
				$image = $file->toImage();

				if ($image->getHeight() > 1080 || $image->getWidth() > 1920) {
					$image->resize(1920, 1080);
				}

				$image->save($link . $name);
				$this->articles->getTable()->where('id', $values['id'])->update(['cover' => $name]);

				$this->cropper('upload/articles/' . $values['id'] . '/' . $name, $this->configuration->newAspectRatio, $this->link('this', ['id' => $values['id']]));
			}

			$this->redirect('this', ['id' => $values['id']]);
		};

		return $form;
	}

	public function handleDeleteImage($articleId): void
	{
		/** @var ActiveRow $image */
		$article = $this->articles->get($articleId);
		unlink(WWW . '/upload/articles/' . $article->id . '/' . $article->cover);
		$article->update(['cover' => null]);
		$this->flashMessage('Náhledový obrázek byl smazán', 'success');
		$this->redirect('this');
	}

	protected function createComponentArticleGrid(): ArticleGrid
	{
		return $this->articleGridFactory->create();
	}

}
