<?php

namespace Src\Common\Infrastructure\Laravel\Middleware;


use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequest extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {

        return array_merge(parent::share($request), [
            'flash' => [
                'successMessage' => fn () => $request->session()->get('successMessage'),
                'errorMessage'  => fn () => $request->session()->get('errorMessage')
            ],
            'notifications' => getNotifications() != null ? getNotifications()['notifications'] : null,
            'unreadNotificationsCount' => getNotifications() != null ? getNotifications()['unread'] : 0,
            'auth' => auth()->user()
        ]);
    }
}
