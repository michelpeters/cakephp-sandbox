<?php
namespace Sandbox\Controller;

use Cake\Event\Event;

class SearchExamplesController extends SandboxAppController {

	public $modelClass = 'Sandbox.CountryRecords';

	public $components = ['Search.Prg'];

	public $helpers = ['Data.Data'];

	public function beforeFilter(Event $event) {
		$this->Auth->allow();

		parent::beforeFilter($event);
	}

	public function index() {
		$this->Prg->commonProcess();

		$countries = $this->paginate($this->CountryRecords->find('searchable', $this->Prg->parsedParams()));
		$this->set(compact('countries'));
	}

}
