<?php declare(strict_types = 1);

namespace App\Service;

use K2D\Core\Models\ConfigurationModel;
use K2D\Core\Models\LogModel;
use K2D\Core\Service\ModelRepository;
use K2D\File\Model\FileModel;
use K2D\File\Model\FolderModel;
use Nette\Database\Table\ActiveRow;

/**
 * @property-read FolderModel $folder
 * @property-read FileModel $file
 */
class ProjectModelRepository extends ModelRepository {

	// FILE
	public function getFolder(int $folder_id): ActiveRow
	{
		return $this->folder->getTable('folder')->where('id', $folder_id)->fetch();
	}

	public function getFilesByFolderId(int $folder_id): array
	{
		return $this->file->getTable('file')->where('folder_id', $folder_id)->order('id DESC')->fetchAll();
	}

	// FOLDER
	public function getSubfolders(?int $parentFolderId, bool $baseOnly = true): array
	{
		$sql = $this->folder->getTable()->where('parent_id', $parentFolderId)->order('name ASC');

		if ($baseOnly) {
			$sql->where('base', 1);
		}

		return $sql->fetchAll();
	}
}
