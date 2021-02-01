<?php
namespace Base;

use App\Model\Admin;

abstract class AbstractController
{
    /** @var View */
    protected $view;
    /** @var Admin */
    protected $user;
    protected function redirect(string $url)
    {
        throw new RedirectException($url);
    }

    /**
     * @param View $view
     */
    public function setView(View $view): void
    {
        $this->view = $view;
    }

    public function setUser(Admin $user): void
    {
        $this->user = $user;
    }

}