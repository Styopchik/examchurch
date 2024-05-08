<?php

namespace App\Http\Controllers;

use App\Facades\UtilityFacades;
use App\Models\User;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function changeLanquage($lang)
    {
        $user       = Auth::user();
        $user->lang = $lang;
        $user->save();
        return redirect()->back()->with('success', __('Language updated successfully.'));
    }

    public function manageLanguage($currantLang)
    {
        $languages      = UtilityFacades::languages();
        $dir            = base_path() . '/resources/lang/' . $currantLang;
        if (!is_dir($dir)) {
            $dir        = base_path() . '/resources/lang/en';
        }
        $arrLabel   = json_decode(file_get_contents($dir . '.json'));
        $arrFiles   = array_diff(
            scandir($dir),
            array(
                '..',
                '.',
            )
        );
        $arrMessage     = [];
        foreach ($arrFiles as $file) {
            $fileName = basename($file, ".php");
            $fileData = $myArray = include $dir . "/" . $file;
            if (is_array($fileData)) {
                $arrMessage[$fileName] = $fileData;
            }
        }
        return view('lang.index', compact('languages', 'currantLang', 'arrLabel', 'arrMessage'));
    }

    public function storeLanguageData(Request $request, $currantLang)
    {
        if (\Auth::user()->can('create-language')) {
            $dir        = base_path() . '/resources/lang/';
            if (!is_dir($dir)) {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $jsonFile   = $dir . "/" . $currantLang . ".json";
            if (isset($request->label) && !empty($request->label)) {
                file_put_contents($jsonFile, json_encode($request->label));
            }
            $langFolder = $dir . "/" . $currantLang;
            if (!is_dir($langFolder)) {
                mkdir($langFolder);
                chmod($langFolder, 0777);
            }
            if (isset($request->message) && !empty($request->message)) {
                foreach ($request->message as $fileName => $fileData) {
                    $content = "<?php return [";
                    $content .= Self::buildArray($fileData);
                    $content .= "];";
                    file_put_contents($langFolder . "/" . $fileName . '.php', $content);
                }
            }
            return redirect()->route('manage.language', [$currantLang])->with('success', __('Language save successfully.'));
        } else {
            return redirect()->back();
        }
    }

    public function buildArray($fileData)
    {
        $content = "";
        foreach ($fileData as $lable => $data) {
            if (is_array($data)) {
                $content .= "'$lable'=>[" . Self::buildArray($data) . "],";
            } else {
                $content .= "'$lable'=>'" . addslashes($data) . "',";
            }
        }
        return $content;
    }

    public function createLanguage()
    {
        return view('lang.create');
    }

    public function storeLanguage(Request $request)
    {
        if (\Auth::user()->can('create-language')) {
            $fileSystem = new Filesystem();
            $langCode   = strtolower($request->code);
            $langDir    = base_path() . '/resources/lang/';
            $dir        = $langDir;
            if (!is_dir($dir)) {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $dir      = $dir . '/' . $langCode;
            $jsonFile = $dir . ".json";
            \File::copy($langDir . 'en.json', $jsonFile);
            if (!is_dir($dir)) {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $fileSystem->copyDirectory($langDir . "en", $dir . "/");
            return redirect()->route('manage.language', [$langCode])->with('success', __('Language created successfully.'));
        } else {
            return redirect()->back();
        }
    }

    public function destroyLanguage($lang)
    {
        if (\Auth::user()->can('delete-language')) {
            $defaultLang    = env('default_language') ?? 'en';
            $langDir        = base_path() . '/resources/lang/';
            if (is_dir($langDir)) {
                UtilityFacades::deleteDirectory($langDir . $lang);
                unlink($langDir . $lang . '.json');
                User::where('lang', 'LIKE', $lang)->update(['lang' => $defaultLang]);
            }
            return redirect()->route('manage.language', $defaultLang)->with('success', __('Language deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }
}
