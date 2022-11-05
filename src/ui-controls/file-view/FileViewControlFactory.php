<?php

namespace Ludoi\UIControls;

interface FileViewControlFactory
{
	public function create(): FileViewControl;
}