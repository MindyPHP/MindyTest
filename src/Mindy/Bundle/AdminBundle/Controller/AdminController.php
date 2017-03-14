<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Controller;

use Mindy\Bundle\AdminBundle\Form\AuthForm;
use Mindy\Bundle\MindyBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminController extends Controller
{
    /**
     * @param Response $response
     *
     * @return Response
     */
    protected function preventCache(Response $response)
    {
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('max-age', 0);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        return $response;
    }

    public function indexAction(Request $request)
    {
        $response = $this->render('admin/index.html', [
            'breadcrumbs' => [
                ['name' => 'Рабочий стол'],
            ],
            'dashboard' => $this->has('dashboard') ? $this->get('dashboard') : null,
            'adminMenu' => $this->get('admin.menu')->getMenu(),
        ]);

        return $this->preventCache($response);
    }

    public function dispatchAction(Request $request, $admin, $action)
    {
        $id = $this->get('admin.registry')->resolveAdmin($admin);
        if (empty($id)) {
            throw new NotFoundHttpException('Unknown admin class');
        }

        $response = $this->forward(sprintf('%s:%sAction', $id, $action), [
            'request' => $request,
        ], $request->query->all());

        return $this->preventCache($response);
    }

    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(AuthForm::class, [], [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_login')
        ]);

        return $this->render('admin/_login.html', [
            'last_username' => $lastUsername,
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    public function logoutAction()
    {
    }
}
