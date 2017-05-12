<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 5/12/17
 * Time: 1:27 PM
 */

namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PageController
 * @package AppBundle\Controller
 */
class PageController extends Controller
{
    /**
 * @Route("/backoffice/page/{id}/delete", name="backoffice_page_delete")
 * @param $id
 * @return \Symfony\Component\HttpFoundation\RedirectResponse
 */
    public function deleteAction($id)
    {
        $this->get('app.pageRepositoryService')->deleteById($id);
    }
}