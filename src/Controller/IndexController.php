<?php
declare(strict_types=1);

namespace App\Controller;

use App\View\View;
use App\Model\TaskModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class IndexController
 *
 * @package App\Controller
 */
final class IndexController implements ControllerInterface
{
    public const SORT_BY_CREATED_AT = 'created_at';
    public const SORT_BY_STATUS = 'status';
    public const SORT_BY_USER_NAME = 'user_name';
    public const SORT_BY_USER_EMAIL = 'user_email';

    public const SORT_ORDER_ASK = 'asc';
    public const SORT_ORDER_DESC = 'desc';

    private const TASKS_PER_PAGE = 3;

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $model = new TaskModel();

        $cookie = $request->getCookieParams();
        $get = $request->getQueryParams();

        $orderBy = $get['orderBy'] ?? self::SORT_ORDER_DESC;
        $sortBy = $get['sortBy'] ?? self::SORT_BY_CREATED_AT;

        $tasks = $model->getAll();
        $pages = ceil(count($tasks) / self::TASKS_PER_PAGE);
        $currPage = (int)max((int)($get['page'] ?? 1), 1);
        $currPage = (int)min($currPage, $pages);

        if ($sortBy === self::SORT_BY_STATUS) {
            $tasks = $model->getPageSortByStatus($currPage, self::TASKS_PER_PAGE, $orderBy);
        } elseif ($sortBy === self::SORT_BY_USER_NAME) {
            $tasks = $model->getPageSortByName($currPage, self::TASKS_PER_PAGE, $orderBy);
        } elseif ($sortBy === self::SORT_BY_USER_EMAIL) {
            $tasks = $model->getPageSortByMail($currPage, self::TASKS_PER_PAGE, $orderBy);
        } elseif ($sortBy === self::SORT_BY_CREATED_AT) {
            $tasks = $model->getPageSortByCreated($currPage, self::TASKS_PER_PAGE, $orderBy);
        }

        $view = (new View())
            ->set('tasks', $tasks)
            ->set('userLogin', $cookie['login'] ?? null)
            ->set('pages', $pages)
            ->set('curr_page', $currPage)
            ->set('order_by', $orderBy)
            ->set('sort_by', $sortBy);

        $html = $view->render('index');

        $response->getBody()->write($html);

        return $response;
    }
}
