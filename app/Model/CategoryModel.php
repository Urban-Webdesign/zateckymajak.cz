<?php declare(strict_types = 1);

namespace App\Model;

use K2D\Core\Models\BaseModel;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class CategoryModel extends BaseModel
{
	protected string $table = 'categories';

	public function getCategories(): array
	{
		return $this->getTable()->fetchAll();
	}

	public function getCategoryById(int $category_id): ActiveRow
	{
		return $this->getTable()->where('id', $category_id)->fetch();
	}

	public function getCategoryBySlug($slug): ActiveRow
	{
		return $this->getTable()->where('slug', $slug)->fetch();
	}
}
