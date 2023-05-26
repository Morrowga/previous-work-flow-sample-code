<?php

namespace Src\BlendedConcept\System\Presentation\HTTP;

use Src\BlendedConcept\System\Application\UseCases\Queries\GetSiteSetting;
use Src\Common\Infrastructure\Laravel\Controller;
use Inertia\Inertia;
use Src\BlendedConcept\Infrastructure\EloquentModels\SiteSettingEloquentModel;
use Src\BlendedConcept\System\Application\DTO\SiteSettingData;
use Src\BlendedConcept\System\Application\UseCases\Commands\UpdateSiteSettingCommand;
use Src\BlendedConcept\System\Domain\Requests\UpdateSettingRequest;

class SettingController extends Controller
{



    /**
     * Display the site settings index page.
     *
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function index()
    {
        try {
            // Authorize the user to view the site settings
            $this->authorize('view', SiteSettingEloquentModel::class);

            // Retrieve the site setting
            $setting = (new GetSiteSetting())->handle();

            // Render the inertia view with the site setting data
            return Inertia::render('BlendedConcept/System/Presentation/Resources/Settings/Index', compact('setting'));
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the execution of the code
            return Inertia::render('BlendedConcept/System/Presentation/Resources/Settings/Index')->with('systemErrorMessage', $e->getMessage());
        }
    }


    /**
     * Update the site setting.
     *
     * @param UpdateSettingRequest $request
     * @return
     */
    public function UpdateSetting(UpdateSettingRequest $request)
    {
        try {
            // Create a SiteSettingData instance from the request
            $site_setting = SiteSettingData::fromRequest($request);

            // Create an UpdateSiteSettingCommand instance with the site setting
            $update_setting = new UpdateSiteSettingCommand($site_setting);

            // Execute the update setting command
            $update_setting->execute();
        } catch (\Exception $e) {
            // Return a response indicating the error to the user
            dd($e->getMessage());
        }
    }
}
