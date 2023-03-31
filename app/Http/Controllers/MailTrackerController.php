<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\Repositories\EmailAccountMessageRepository;

class MailTrackerController extends Controller
{
    public function __construct(protected EmailAccountMessageRepository $repository)
    {
    }

    public function opens($hash)
    {
        $response = response($this->createPixel(), 200)
            ->header('Content-type', 'image/gif')
            ->header('Content-Length', 42)
            ->header('Cache-Control', 'private, no-cache, no-cache=Set-Cookie, proxy-revalidate')
            ->header('Expires', 'Wed, 11 Jan 2000 12:59:00 GMT')
            ->header('Last-Modified', 'Wed, 11 Jan 2006 12:59:00 GMT')
            ->header('Pragma', 'no-cache');

        if (! $this->isWithinApplication()) {
            if ($message = $this->repository->findByHash($hash)) {
                $this->repository->recordOpen($message);
            }
        }

        return $response;
    }

    public function link(Request $request)
    {
        $url  = $request->l;
        $hash = $request->h;

        return $this->linkClicked($url, $hash);
    }

    protected function linkClicked($url, $hash)
    {
        if (! $url) {
            $url = '/';
        }

        /**
         * I found that recently we are getting a lot of false clicks/reads because automatic
         * spam protection and phishing protection are checking the urls in our emails as soon as they are sent.
         *
         * The SPAM protection appears to call "HEAD" calls instead of "GET" as they aren't interested in the result,
         * just that things seem to redirect somewhere good.
         */

        if (request()->isMethod('GET') && ! $this->isWithinApplication()) {
            $message = $this->repository->findByHash($hash);

            if ($message) {
                $this->repository->recordLinkClick($message, $url);
            }
        }

        return redirect($url);
    }

    protected function isWithinApplication()
    {
        $referer = request()->headers->get('referer');

        return $referer && rtrim($referer, '/') == rtrim(config('app.url'), '/');
    }

    protected function createPixel()
    {
        // Create a 1x1 transparent pixel
        return sprintf('%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c', 71, 73, 70, 56, 57, 97, 1, 0, 1, 0, 128, 255, 0, 192, 192, 192, 0, 0, 0, 33, 249, 4, 1, 0, 0, 0, 0, 44, 0, 0, 0, 0, 1, 0, 1, 0, 0, 2, 2, 68, 1, 0, 59);
    }
}
