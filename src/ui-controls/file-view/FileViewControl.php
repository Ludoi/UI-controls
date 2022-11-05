<?php

namespace Ludoi\UIControls;

use Nette\Application\UI\Control;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

class FileViewControl extends Control
{
	/**
	 * @var string
	 */
	private string $directory;

	public function __construct(string $directory)
	{
		$this->directory = $directory;
	}

	private function getLogs(): array
	{
		$oneDay = (new DateTime())->sub(new \DateInterval('P1D'))->getTimestamp();
		$sixHours = (new DateTime())->sub(new \DateInterval('PT6H'))->getTimestamp();
		$files = [];
		foreach (array_diff(scandir($this->directory. '/', SCANDIR_SORT_ASCENDING), ['..', '.']) as $fileName) {
			$fileWithPath = $this->directory. '/' . $fileName;
			$filesize = @filesize($fileWithPath);
			$filemtime = @filemtime($fileWithPath);
			$files[] = ['name' => $fileName, 'size' => $filesize, 'mtime' => $filemtime, 'class-size' => ($filesize > 10000000)? 'text-danger' : (($filesize > 1000000)? 'text-warning' : 'text-muted'),
				'class-time' => ($filemtime > $sixHours)? 'text-danger' : (($filemtime > $oneDay)? 'text-warning' : 'text-muted')];
		}
		return $files;
	}

	public function handleDeleteExceptions(): void
	{
		foreach (array_diff(scandir($this->directory. '/', SCANDIR_SORT_ASCENDING), ['..', '.']) as $fileName) {
			if (Strings::startsWith($fileName, 'exception') ||
			    Strings::startsWith($fileName, 'error')) {
				@unlink($this->directory . '/' . $fileName);
			}
		}
		$this->flashMessage('Soubory smazány', 'success');
	}

	public function handleOpen(string $filename): void
	{

	}

	public function render(): void
	{
		$this->template->files = $this->template->getLogs;
		$this->template->render(__DIR__ . '/file-view-control.latte');
	}
}