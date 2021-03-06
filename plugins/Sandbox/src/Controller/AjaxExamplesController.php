<?php
namespace Sandbox\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class AjaxExamplesController extends SandboxAppController {

	public $components = ['Data.CountryProvinceHelper'];

	public $helpers = ['Data.Data'];

	/**
	 * AppController::constructClasses()
	 *
	 * @return void
	 */
	public function initialize() {
		if ($this->request->action === 'redirectingPrevented') {
			$this->components['Ajax.Ajax'] = ['flashKey' => 'FlashMessage'];
		}
		parent::initialize();
	}

	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);

		$this->Auth->allow();
	}

	/**
	 * List of all examples.
	 *
	 * @return void
	 */
	public function index() {
	}

	/**
	 * AjaxExamplesController::simple()
	 *
	 * @return void
	 */
	public function simple() {
		if ($this->request->is(['ajax'])) {
			// Lets create current datetime
			$now = date(FORMAT_DB_DATETIME);
			$this->set('result', ['now' => $now]);
			$this->set('_serialize', ['result']);
		}
	}

	/**
	 * AjaxExamplesController::toggle()
	 *
	 * @return void
	 */
	public function toggle() {
		if ($this->request->is(['ajax'])) {
			// Simulate a DB save via session
			$status = (bool)$this->request->query('status');
			$this->Session->write('AjaxToggle.status', $status);
			$this->set(compact('status'));

			// Manually render
			$View = $this->getView();
			$result = (string)$View->render();
			$View->set(compact('result'));

			// Since we already rendered the snippet, we need to reset the render state
			$View->hasRendered = false;
			$View->set('_serialize', ['result']);
			return;
		}

		// Read from DB (simulated)
		$status = (bool)$this->Session->read('AjaxToggle.status');

		$this->set(compact('status'));
	}

	/**
	 * AJAX Pagination example.
	 *
	 * When the request is ajax, just render the container,
	 * otherwise include the container ctp as element in the main ctp.
	 *
	 * The JS could probably be simplified or made more generic to be put
	 * in a central location and used for all paginations across the website.
	 *
	 * @return void
	 */
	public function pagination() {
		$this->loadModel('Data.Countries');

		$this->Countries->recursive = 0;
		$countries = $this->paginate('Countries');
		$this->set(compact('countries'));

		if ($this->request->is('ajax')) {
			$this->render('pagination_container');
		}
	}

	/**
	 * Main AJAX example for chained dropdowns.
	 * If the first dropdown has been selected, it will trigger an
	 * AJAX call to pre-fill the next dropdown with the appropriate options.
	 *
	 * It should also prefill the previously selected choices on POST.
	 * That is what the component does here.
	 *
	 * @return void
	 */
	public function chainedDropdowns() {
		$this->Users = TableRegistry::get('Users');
		$user = $this->Users->newEntity();

		if ($this->request->is('post')) {
			$this->Users->validator()->add('country_province_id', 'numeric', [
				'rule' => 'numeric',
				'message' => 'Please select something'
			]);
			$user = $this->Users->patchEntity($user, $this->request->data);
			//$result = $this->User->validates();
		}

		$this->CountryProvinceHelper->provideData(false, 'User');
		$this->set(compact('user'));
	}

	/**
	 * My own convention was to suffix AJAX only actions with "_ajax".
	 * Not really necessary, but can maybe distinguish such actions from
	 * the normal ones.
	 *
	 * This method provides the AJAX data chained_dropdowns() needs.
	 *
	 * @return void
	 */
	public function countryProvincesAjax() {
		$this->request->allowMethod('ajax');
		$id = $this->request->query('id');
		if (!$id) {
			throw new NotFoundException();
		}

		$this->viewClass = 'Ajax.Ajax';

		$this->loadModel('Data.CountryProvinces');
		$countryProvinces = $this->CountryProvinces->getListByCountry($id);

		$this->set(compact('countryProvinces'));
	}

	/**
	 * Show how redirecting works when AJAX is involved:
	 * It will requestAction() the redirect instead of actually redirecting.
	 *
	 * @return void
	 */
	public function redirecting() {
		if ($this->request->is('post')) {
			// Do sth like saving data
			if (!$this->request->is('ajax')) {
				$this->Flash->success('Yeah, that was a normal POST and redirect (PRG).');
			}
			return $this->redirect(['action' => 'index']);
		}
	}

	/**
	 * Show how redirecting works when AJAX is involved using Ajax component and view class.
	 * It will not follow the redirect, but instead return it along with messages sent.
	 *
	 * @return void
	 */
	public function redirectingPrevented() {
		if ($this->request->is('post')) {
			// Do sth like saving data

			$this->Flash->success('Yeah, that was a normal POST and redirect (PRG).');

			return $this->redirect(['action' => 'index']);
		}
	}

}
