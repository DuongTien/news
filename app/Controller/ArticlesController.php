<?php
App::uses('AppController', 'Controller');
/**
 * Articles Controller
 *
 * @property Article $Article
 * @property PaginatorComponent $Paginator
 */
class ArticlesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
    public $uses = array('Article', 'Category');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Article->recursive = 0;
		$this->set('articles', $this->Paginator->paginate());
        $this->set('categories', $this->Category->find('list',array('conditions' => array('active' => true))));
    }

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Article->exists($id)) {
			throw new NotFoundException(__('Invalid article'));
		}
		$options = array('conditions' => array('Article.' . $this->Article->primaryKey => $id));
		$this->set('article', $this->Article->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Article->create();
			if ($this->Article->save($this->request->data)) {
				$this->Session->setFlash(__('The article has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The article could not be saved. Please, try again.'));
			}
		}
        else
        {
            $this->set('categories', $this->Category->find('list',array('conditions' => array('active' => true))));
        }
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Article->exists($id)) {
			throw new NotFoundException(__('Invalid article'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Article->save($this->request->data)) {
				$this->Session->setFlash(__('The article has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The article could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Article.' . $this->Article->primaryKey => $id));
            $this->request->data = $this->Article->find('first', $options);
            $this->set('categories', $this->Category->find('list',array('conditions' => array('active' => true))));
        }
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Article->id = $id;
		if (!$this->Article->exists()) {
			throw new NotFoundException(__('Invalid article'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Article->delete()) {
			$this->Session->setFlash(__('The article has been deleted.'));
		} else {
			$this->Session->setFlash(__('The article could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}


    public function index() {
        $this->Paginator->settings['conditions'] = array('active' => true);
        $this->Paginator->settings['limit'] = 5;
        $articles = $this->paginate('Article');
        $this->set(compact('articles'));
    }

    public function articleCategory($id = null) {
        $this->Category->id = $id;
        if (!$this->Category->exists()) {
            throw new NotFoundException(__('Invalid article'));
        }
        $this->Paginator->settings['conditions'] = array('category_id' => $id, 'active' => true);
        $this->Paginator->settings['limit'] = 5;
        $articles = $this->paginate('Article');
        $this->set(compact('articles'));
        $this->render('index');
    }

    public function detail($id = null) {
        $this->Article->id = $id;
        if (!$this->Article->exists()) {
            throw new NotFoundException(__('Invalid article'));
        }

        $article = $this->Article->find('first',array('conditions' => array('id' => $id, 'active' => true)));
        $this->set(compact('article'));
    }

    public function searchArticle() {
        if($this->request->is(array('put','post'))) {
            $key = $this->request->data['Article']['key'];
            $articles = $this->Article->find('all',array('conditions' => array('active' => true, 'title LIKE' => '%'.$key.'%')));
            $this->set(compact('articles'));
        }
        $this->render('index');
    }
}
