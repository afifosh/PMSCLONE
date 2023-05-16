<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Http\Controllers\ApiController;

class LogoController extends ApiController
{
    /**
     * Save company logo
     *
     * @param  string  $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($type, Request $request)
    {
        // Logo store flag

        $logoName = 'logo_'.$type;

        $request->validate([
            $logoName => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        ]);

        $this->destroy($type);

        if ($filename = $request->file($logoName)->store('/', 'public')) {
            $logoUrl = "/storage/$filename";
            settings([$logoName => $logoUrl]);

            return $this->response(['logo' => $logoUrl]);
        }

        abort(500, 'Failed to save the logo.');
    }

    /**
     * Remove company logo
     *
     * @param  string  $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($type)
    {
        $optionName = 'logo_'.$type;
        $currentLogo = settings($optionName);
        $filename = basename($currentLogo);

        if (! empty($currentLogo) && $this->disk()->exists($filename)) {
            $this->disk()->delete($filename);
        }

        settings()->forget($optionName)->save();

        return $this->response('', 204);
    }

    /**
     * Get the logo storage disk
     *
     * @return mixed
     */
    protected function disk()
    {
        return Storage::disk('public');
    }
}
