<?php declare(strict_types = 1);

namespace App\Model;

use K2D\Core\Models\BaseModel;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class ArticleModel extends BaseModel
{

	protected string $table = 'articles';

	public function getArticle(string $slug): ?ActiveRow
	{
		return $this->getTable()->where('public', 1)->where('slug', $slug)->fetch();
	}

	public function getLatestArticle(): ?ActiveRow
	{
		return $this->getTable()->where('public', 1)->order('created DESC')->order('id DESC')->limit(1)->fetch();
	}

	public function getArticles(): Selection
	{
		return $this->getTable()->where('public', 1)->order('created DESC')->order('id DESC');
	}

	public function getArticlesExceptFirst(int $count): Selection
	{
		return $this->getArticles()->limit($count - 1, 1);
	}

	public function getArticlesByCategory(int $category_id): Selection
	{
		return $this->getTable()->where('public', 1)->where('category_id', $category_id)->order('created DESC');
	}

	public function getArticlesByCategoryCount(int $category_id): int
	{
		return $this->getArticlesByCategory($category_id)->count();
	}

	public function getLatestArticlesExceptThisOne(string $slug): Selection
	{
		return $this->getTable()->where('public', 1)->where('slug !=', $slug)->order('created DESC')->order('id DESC')->limit(4);
	}
}
