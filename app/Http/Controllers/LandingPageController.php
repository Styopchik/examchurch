<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facades\UtilityFacades;
use Illuminate\Support\Facades\Storage;
use App\Models\FooterSetting;
use App\Models\HeaderSetting;
use App\Models\PageSetting;
use App\Models\settings;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    private function updateSettings($input)
    {
        foreach ($input as $key => $value) {
            settings::updateOrCreate(
                ['key' => $key, 'created_by' => Auth::user()->id],
                ['value' => $value]
            );
        }
    }

    public function landingPageSetting()
    {
        if (\Auth::user()->can('manage-landingpage')) {
            return view('landing-page.app-setting');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function appSettingStore(Request $request)
    {
        request()->validate([
            'apps_name'             => 'required|string|max:191',
            'apps_bold_name'        => 'required|string|max:191',
            'app_detail'            => 'required|string',
            'apps_image'            => 'image|mimes:png,jpg,jpeg',
        ]);
        $appMultipleImage = [];
        if ($request->hasFile('apps_multiple_image')) {
            $images = $request->file('apps_multiple_image');
            foreach ($images as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('landing-page/app/', $imageName);
                $appMultipleImage[] = ['apps_multiple_image' => 'landing-page/app/' . $imageName];
            }
        }
        $appSettingData = [
            'apps_setting_enable'           => ($request->apps_setting_enable && $request->apps_setting_enable == 'on') ? 'on' : 'off',
            'apps_name'                     => $request->apps_name,
            'apps_bold_name'                => $request->apps_bold_name,
            'app_detail'                    => $request->app_detail,
            'apps_multiple_image_setting'   => $appMultipleImage ? json_encode($appMultipleImage) : null,
        ];
        if ($request->hasFile('apps_image')) {
            $imageName = 'app.' . $request->apps_image->extension();
            $request->apps_image->storeAs('landing-page/app/', $imageName);
            $appSettingData['apps_image'] = 'landing-page/app/' . $imageName;
        }
        Self::updateSettings($appSettingData);
        return redirect()->back()->with('success', __('App setting updated successfully.'));
    }

    public function menuSetting()
    {
        return view('landing-page.menu.index');
    }

    public function menuSettingSection1Store(Request $request)
    {
        request()->validate([
            'menu_name_section1'                => 'required|string|max:191',
            'menu_bold_name_section1'           => 'required|string|max:191',
            'menu_detail_section1'              => 'required|string',
            'menu_image_section1'               => 'image|mimes:png,jpg,jpeg',
        ]);
        $menuSettingSection1Data = [
            'menu_setting_section1_enable'      => ($request->menu_setting_section1_enable && $request->menu_setting_section1_enable == 'on') ? 'on' : 'off',
            'menu_name_section1'                => $request->menu_name_section1,
            'menu_bold_name_section1'           => $request->menu_bold_name_section1,
            'menu_detail_section1'              => $request->menu_detail_section1,
        ];
        if ($request->hasFile('menu_image_section1')) {
            $imageName = 'menusection1.' . $request->menu_image_section1->extension();
            $request->menu_image_section1->storeAs('landing-page/menu/', $imageName);
            $menuSettingSection1Data['menu_image_section1'] = 'landing-page/menu/' . $imageName;
        }
        Self::updateSettings($menuSettingSection1Data);
        return redirect()->back()->with('success', __('Menu setting updated successfully.'));
    }

    public function menuSettingSection2Store(Request $request)
    {
        request()->validate([
            'menu_name_section2'            => 'required|string|max:191',
            'menu_bold_name_section2'       => 'required|string|max:191',
            'menu_detail_section2'          => 'required|string',
            'menu_image_section2'           => 'image|mimes:png,jpg,jpeg',
        ]);
        $menuSettingSection2Data = [
            'menu_setting_section2_enable'  => ($request->menu_setting_section2_enable && $request->menu_setting_section2_enable == 'on') ? 'on' : 'off',
            'menu_name_section2'            => $request->menu_name_section2,
            'menu_bold_name_section2'       => $request->menu_bold_name_section2,
            'menu_detail_section2'          => $request->menu_detail_section2,
        ];
        if ($request->hasFile('menu_image_section2')) {
            $imageName = 'menusection12.' . $request->menu_image_section2->extension();
            $request->menu_image_section2->storeAs('landing-page/menu/', $imageName);
            $menuSettingSection2Data['menu_image_section2'] = 'landing-page/menu/' . $imageName;
        }
        Self::updateSettings($menuSettingSection2Data);
        return redirect()->back()->with('success', __('Menu setting updated successfully.'));
    }

    public function menuSettingSection3Store(Request $request)
    {
        request()->validate([
            'menu_name_section3'            => 'required|string|max:191',
            'menu_bold_name_section3'       => 'required|string|max:191',
            'menu_detail_section3'          => 'required|string',
            'menu_image_section3'           => 'image|mimes:png,jpg,jpeg',
        ]);
        $menuSettingSection3Data = [
            'menu_setting_section3_enable'  => $request->menu_setting_section3_enable && $request->menu_setting_section3_enable == 'on' ? 'on' : 'off',
            'menu_name_section3'            => $request->menu_name_section3,
            'menu_bold_name_section3'       => $request->menu_bold_name_section3,
            'menu_detail_section3'          => $request->menu_detail_section3,
        ];
        if ($request->hasFile('menu_image_section3')) {
            $imageName = 'menusection13.' . $request->menu_image_section3->extension();
            $request->menu_image_section3->storeAs('landing-page/menu/', $imageName);
            $menuSettingSection3Data['menu_image_section3'] = 'landing-page/menu/' . $imageName;
        }
        Self::updateSettings($menuSettingSection3Data);
        return redirect()->back()->with('success', __('Menu setting updated successfully.'));
    }

    public function faqSetting()
    {
        return view('landing-page.faq-setting');
    }

    public function faqSettingStore(Request $request)
    {
        request()->validate([
            'faq_name'              => 'required|string|max:191',
        ]);
        $faqSettingData = [
            'faq_setting_enable'    => ($request->faq_setting_enable && $request->faq_setting_enable == 'on') ? 'on' : 'off',
            'faq_name'              => $request->faq_name,
        ];
        Self::updateSettings($faqSettingData);
        return redirect()->back()->with('success', __('Faq setting updated successfully.'));
    }

    public function featureSetting(Request $request)
    {
        $featureSettings = json_decode(UtilityFacades::keysettings('feature_setting', 1), true) ?? [];
        return view('landing-page.feature.index', compact('featureSettings'));
    }

    public function featureSettingStore(Request $request)
    {
        request()->validate([
            'feature_name'              => 'required|string|max:191',
            'feature_bold_name'         => 'required|string|max:191',
            'feature_detail'            => 'required|string',
        ]);
        $featureData = [
            'feature_setting_enable'    => ($request->feature_setting_enable && $request->feature_setting_enable == 'on') ? 'on' : 'off',
            'feature_name'              => $request->feature_name,
            'feature_bold_name'         => $request->feature_bold_name,
            'feature_detail'            => $request->feature_detail,
        ];
        Self::updateSettings($featureData);
        return redirect()->back()->with('success', __('Feature setting updated successfully.'));
    }

    public function featureCreate()
    {
        return view('landing-page.feature.create');
    }

    public function featureStore(Request $request)
    {
        request()->validate([
            'feature_name'              => 'required|string|max:191',
            'feature_bold_name'         => 'required|string|max:191',
            'feature_detail'            => 'required|string',
            'feature_image'             => 'required|image|mimes:svg',
        ]);
        $settingData = [
            "feature_setting" => UtilityFacades::keysettings('feature_setting', 1),
        ];
        $settings = $settingData;
        $featureData = json_decode($settings['feature_setting'], true);
        if ($request->hasFile('feature_image')) {
            $featureImage = time() . "-feature_image." . $request->feature_image->getClientOriginalExtension();
            $imageName = $featureImage;
            $request->feature_image->storeAs('landing-page/feature', $imageName);
            $featureDatas['feature_image'] = 'landing-page/feature/' . $imageName;
        }
        $featureDatas['feature_name']          = $request->feature_name;
        $featureDatas['feature_bold_name']     = $request->feature_bold_name;
        $featureDatas['feature_detail']        = $request->feature_detail;
        $featureData[]                         = $featureDatas;
        $featureData                           = json_encode($featureData);
        settings::updateOrCreate(
            ['key'      => 'feature_setting'],
            ['value'    => $featureData]
        );
        return redirect()->back()->with(['success' => __('Feature setting created successfully.')]);
    }

    public function featureEdit($key)
    {
        $setting_data = [
            "feature_setting" => UtilityFacades::keysettings('feature_setting', 1),
        ];
        $settings = $setting_data;
        $features = json_decode($settings['feature_setting'], true);
        $feature = $features[$key];
        return view('landing-page.feature.edit', compact('feature', 'key'));
    }

    public function featureUpdate(Request $request, $key)
    {
        request()->validate([
            'feature_name'                         => 'required|string|max:191',
            'feature_bold_name'                    => 'required|string|max:191',
            'feature_detail'                       => 'required|string',
            'feature_image'                        => 'required|image|mimes:svg',
        ]);
        $settingData = [
            "feature_setting" => UtilityFacades::keysettings('feature_setting', 1),
        ];
        $settings = $settingData;
        $featureData = json_decode($settings['feature_setting'], true);
        if ($request->hasFile('feature_image')) {
            $featureImage                          = time() . "-feature_image." . $request->feature_image->getClientOriginalExtension();
            $imageName                             = $featureImage;
            $request->feature_image->storeAs('landing-page/feature', $imageName);
            $featureData[$key]['feature_image']    = 'landing-page/feature/' . $imageName;
        }
        $featureData[$key]['feature_name']         = $request->feature_name;
        $featureData[$key]['feature_bold_name']    = $request->feature_bold_name;
        $featureData[$key]['feature_detail']       = $request->feature_detail;
        $featureData                               = json_encode($featureData);
        settings::updateOrCreate(
            ['key'      => 'feature_setting'],
            ['value'    => $featureData]
        );
        return redirect()->back()->with(['success' => __('Feature setting updated successfully.')]);
    }

    public function featureDelete($key)
    {
        $settingData = [
            "feature_setting" => UtilityFacades::keysettings('feature_setting', 1),
        ];
        $pages = json_decode($settingData['feature_setting'], true);
        unset($pages[$key]);
        settings::updateOrCreate(['key' =>  'feature_setting'], ['value' => $pages]);
        return redirect()->back()->with(['success' => __('Feature setting deleted successfully')]);
    }

    public function startViewSetting()
    {
        return view('landing-page.start-view-setting');
    }

    public function startViewSettingStore(Request $request)
    {
        request()->validate([
            'start_view_name'           => 'required|string|max:191',
            'start_view_detail'         => 'required|string',
            'start_view_link_name'      => 'required|string|max:191',
            'start_view_link'           => 'required|url',
            'start_view_image'          => 'image|mimes:png,jpg,jpeg',
        ]);
        $startViewSettingData = [
            'start_view_setting_enable' => ($request->start_view_setting_enable && $request->start_view_setting_enable == 'on') ? 'on' : 'off',
            'start_view_name'           => $request->start_view_name,
            'start_view_detail'         => $request->start_view_detail,
            'start_view_link_name'      => $request->start_view_link_name,
            'start_view_link'           => $request->start_view_link,
        ];
        if ($request->hasFile('start_view_image')) {
            $imageName                  = 'startview.' . $request->start_view_image->extension();
            $request->start_view_image->storeAs('landing-page', $imageName);
            $startViewSettingData['start_view_image']   = 'landing-page/' . $imageName;
        }
        Self::updateSettings($startViewSettingData);
        return redirect()->back()->with('success', __('Start view setting updated successfully.'));
    }

    public function businessGrowthSetting(Request $request)
    {
        $settingData = [
            "business_growth_setting"       => UtilityFacades::keysettings('business_growth_setting', 1),
            "business_growth_view_setting"  => UtilityFacades::keysettings('business_growth_view_setting', 1),
        ];
        $settings                           = $settingData;
        $businessGrowthSettings             = json_decode($settings['business_growth_setting'], true) ?? [];
        $businessGrowthViewSettings         = json_decode($settings['business_growth_view_setting'], true);
        return view('landing-page.business-growth.index', compact('businessGrowthSettings', 'businessGrowthViewSettings'));
    }

    public function businessGrowthSettingStore(Request $request)
    {
        request()->validate([
            'business_growth_name'          => 'required|string|max:191',
            'business_growth_bold_name'     => 'required|string|max:191',
            'business_growth_detail'        => 'required|string',
            'business_growth_video'         => 'mimes:mp4,avi,wmv,mov,webm',
            'business_growth_front_image'   => 'image|mimes:png,jpg,jpeg',
        ]);
        $businessGrowthData = [
            'business_growth_setting_enable'    => ($request->business_growth_setting_enable && $request->business_growth_setting_enable == 'on') ? 'on' : 'off',
            'business_growth_name'              => $request->business_growth_name,
            'business_growth_bold_name'         => $request->business_growth_bold_name,
            'business_growth_detail'            => $request->business_growth_detail,
        ];
        if ($request->hasFile('business_growth_front_image')) {
            $image_name                                         = '10.' . $request->business_growth_front_image->extension();
            $request->business_growth_front_image->storeAs('landing-page/businessgrowth/', $image_name);
            $businessGrowthData['business_growth_front_image']  = 'landing-page/businessgrowth/' . $image_name;
        }
        if ($request->hasFile('business_growth_video')) {
            $fileName                                    = 'video.' . $request->business_growth_video->extension();
            $request->business_growth_video->storeAs('landing-page/businessgrowth/', $fileName);
            $businessGrowthData['business_growth_video'] = $request->business_growth_video->storeAs('landing-page/businessgrowth/', $fileName);
        }
        Self::updateSettings($businessGrowthData);
        return redirect()->back()->with('success', __('Business growth updated successfully.'));
    }

    public function businessGrowthCreate()
    {
        return view('landing-page.business-growth.create');
    }

    public function businessGrowthStore(Request $request)
    {
        request()->validate([
            'business_growth_title'     => 'required|string|max:191',
        ]);
        $settingData = [
            "business_growth_setting"   => UtilityFacades::keysettings('business_growth_setting', 1),
        ];
        $settings                       = $settingData;
        $data                           = json_decode($settings['business_growth_setting'], true);
        $datas['business_growth_title'] = $request->business_growth_title;
        $data[]                         = $datas;
        $data                           = json_encode($data);
        settings::updateOrCreate(
            ['key'      => 'business_growth_setting'],
            ['value'    => $data]
        );
        return redirect()->back()->with(['success' => __('Business growth setting created successfully.')]);
    }

    public function businessGrowthEdit($key)
    {
        $settingData = [
            "business_growth_setting" => UtilityFacades::keysettings('business_growth_setting', 1),
        ];
        $settings = $settingData;
        $businessGrowthSettings       = json_decode($settings['business_growth_setting'], true);
        $businessGrowthSetting        = $businessGrowthSettings[$key];
        return view('landing-page.business-growth.edit', compact('businessGrowthSetting', 'key'));
    }

    public function businessGrowthUpdate(Request $request, $key)
    {
        $settingData = [
            "business_growth_setting"           => UtilityFacades::keysettings('business_growth_setting', 1),
        ];
        $settings                               = $settingData;
        $data                                   = json_decode($settings['business_growth_setting'], true);
        $data[$key]['business_growth_title']    = $request->business_growth_title;
        $data                                   = json_encode($data);
        settings::updateOrCreate(
            ['key'      => 'business_growth_setting'],
            ['value'    => $data]
        );
        return redirect()->back()->with(['success' => __('Business growth setting updated successfully.')]);
    }

    public function businessGrowthDelete($key)
    {
        $settingData = [
            "business_growth_setting"   => UtilityFacades::keysettings('business_growth_setting', 1),
        ];
        $pages                          = json_decode($settingData['business_growth_setting'], true);
        unset($pages[$key]);
        settings::updateOrCreate(['key' =>  'business_growth_setting'], ['value' => $pages]);
        return redirect()->back()->with(['success' => __('Business growth setting deleted successfully.')]);
    }

    public function businessGrowthViewCreate()
    {
        return view('landing-page.business-growth.business-growth-view-create');
    }

    public function businessGrowthViewStore(Request $request)
    {
        request()->validate([
            'business_growth_view_name'         => 'required|string|max:191',
            'business_growth_view_amount'       => 'required|numeric',
        ]);
        $settingData = [
            "business_growth_view_setting"      => UtilityFacades::keysettings('business_growth_view_setting', 1),
        ];
        $settings                               = $settingData;
        $data                                   = json_decode($settings['business_growth_view_setting'], true);
        $datas['business_growth_view_name']     = $request->business_growth_view_name;
        $datas['business_growth_view_amount']   = $request->business_growth_view_amount;
        $data[]                                 = $datas;
        $data                                   = json_encode($data);
        settings::updateOrCreate(
            ['key'      => 'business_growth_view_setting'],
            ['value'    => $data]
        );
        return redirect()->back()->with(['success' => __('Business growth view setting created successfully.')]);
    }

    public function businessGrowthViewEdit($key)
    {
        $settingData = [
            "business_growth_view_setting"          => UtilityFacades::keysettings('business_growth_view_setting', 1),
        ];
        $settings                                   = $settingData;
        $businessGrowthViewSettings                 = json_decode($settings['businessGrowthViewSetting'], true);
        $businessGrowthViewSetting                  = $businessGrowthViewSettings[$key];
        return view('landing-page.business-growth.business-growth-view-edit', compact('businessGrowthViewSetting', 'key'));
    }

    public function businessGrowthViewUpdate(Request $request, $key)
    {
        request()->validate([
            'business_growth_view_name'         => 'required|string|max:191',
            'business_growth_view_amount'       => 'required|numeric',
        ]);
        $settingData = [
            "business_growth_view_setting"          => UtilityFacades::keysettings('business_growth_view_setting', 1),
        ];
        $settings                                   = $settingData;
        $data                                       = json_decode($settings['business_growth_view_setting'], true);
        $data[$key]['business_growth_view_name']    = $request->business_growth_view_name;
        $data[$key]['business_growth_view_amount']  = $request->business_growth_view_amount;
        $data                                       = json_encode($data);
        settings::updateOrCreate(
            ['key'      => 'business_growth_view_setting'],
            ['value'    => $data]
        );
        return redirect()->back()->with(['success' => __('Business growth view setting updated successfully.')]);
    }

    public function businessGrowthViewDelete($key)
    {
        $settingData = [
            "business_growth_view_setting"          => UtilityFacades::keysettings('business_growth_view_setting', 1),
        ];
        $pages                                      = json_decode($settingData['business_growth_view_setting'], true);
        unset($pages[$key]);
        settings::updateOrCreate(['key' =>  'business_growth_view_setting'], ['value' => $pages]);
        return redirect()->back()->with(['success' => __('Business growth view setting deleted successfully.')]);
    }

    public function contactusSetting()
    {
        return view('landing-page.contactus-setting');
    }

    public function contactusSettingStore(Request $request)
    {
        request()->validate([
            'contactus_name'                => 'required|string|max:191',
            'contactus_bold_name'           => 'required|string|max:191',
            'contactus_detail'              => 'required|string',
            'contact_email'                 => 'required|email',
            'latitude'                      => 'required|numeric',
            'longitude'                     => 'required|numeric',
        ]);
        $data = [
            'contactus_setting_enable'      => ($request->contactus_setting_enable && $request->contactus_setting_enable == 'on') ? 'on' : 'off',
            'contactus_name'                => $request->contactus_name,
            'contactus_bold_name'           => $request->contactus_bold_name,
            'contactus_detail'              => $request->contactus_detail,
            'contact_email'                 => $request->contact_email,
            'latitude'                      => $request->latitude,
            'longitude'                     => $request->longitude,
        ];
        Self::updateSettings($data);
        return redirect()->back()->with('success', __('Contactus setting updated successfully.'));
    }


    public function blogSetting()
    {
        return view('landing-page.blog-setting');
    }

    public function blogSettingStore(Request $request)
    {
        request()->validate([
            'blog_name'             => 'required|string|max:191',
            'blog_detail'           => 'required|string',
        ]);
        $data = [
            'blog_setting_enable'   => ($request->blog_setting_enable && $request->blog_setting_enable == 'on') ? 'on' : 'off',
            'blog_name'             => $request->blog_name,
            'blog_detail'           => $request->blog_detail,
        ];
        Self::updateSettings($data);
        return redirect()->back()->with('success', __('Blog setting updated successfully.'));
    }

    public function footerSetting(Request $request)
    {
        $footerMainMenus    = FooterSetting::where('parent_id', 0)->get();
        $footerSubMenus     = FooterSetting::where('parent_id', '!=', 0)->get();
        return view('landing-page.footer-settings', compact('footerMainMenus', 'footerSubMenus'));
    }

    public function footerSettingStore(Request $request)
    {
        request()->validate([
            'footer_description'        => 'required|string',
        ]);
        $data = [
            'footer_setting_enable'     => ($request->footer_setting_enable && $request->footer_setting_enable == 'on') ? 'on' : 'off',
            'footer_description'        => $request->footer_description,
        ];
        Self::updateSettings($data);
        return redirect()->back()->with('success', __('Footer setting updated successfully.'));
    }

    public function footerMainMenuCreate()
    {
        return view('landing-page.footer.create');
    }

    public function footerMainMenuStore(Request $request)
    {
        request()->validate([
            'menu'                     => 'required|string|max:191|unique:footer_settings,menu',
        ]);
        $footerMainMenu                = new FooterSetting();
        $footerMainMenu->menu          = $request->menu;
        $footerMainMenu->parent_id     = 0;
        $footerMainMenu->save();
        return redirect()->back()->with('success', __('Footer Main Menu created successfully'));
    }

    public function footerMainMenuEdit($id)
    {
        $footerMainMenuEdit = FooterSetting::where('id', $id)->first();
        return view('landing-page.footer.edit', compact('footerMainMenuEdit'));
    }

    public function footerMainMenuUpdate(Request $request, $id)
    {
        request()->validate([
            'menu'                 => 'required|string|max:191',
        ]);
        $footerMainMenu            = FooterSetting::where('id', $id)->first();
        $footerMainMenu->menu      = $request->menu;
        $footerMainMenu->parent_id = 0;
        $footerMainMenu->save();
        return redirect()->back()->with('success', __('Footer Main Menu updated successfully'));
    }

    public function footerMainMenuDelete($id)
    {
        $footerMainMenu = FooterSetting::find($id);
        if ($footerMainMenu->parent_id == 0) {
            FooterSetting::where('parent_id', $id)->delete();
        }
        $footerMainMenu->delete();
        return redirect()->back()->with('success', 'Footer Menu Updated Successfully');
    }

    public function footerSubMenuCreate()
    {
        $pages      = PageSetting::pluck('title', 'id');
        $footers    = FooterSetting::where('parent_id', 0)->pluck('menu', 'id');
        return view('landing-page.footer.create-sub-menu', compact('footers', 'pages'));
    }

    public function footerSubMenuStore(Request $request)
    {
        $pages                       = PageSetting::find($request->page_id);
        request()->validate([
            'page_id'                 => 'required|string|unique:footer_settings,page_id',
        ],[
            'page_id.unique' => 'The page name has already been taken.',
        ]);
        $footerSubMenu               = new FooterSetting();
        $footerSubMenu->menu         = $pages->title;
        $footerSubMenu->page_id      = $request->page_id;
        $footerSubMenu->parent_id    = $request->parent_id;
        $footerSubMenu->save();
        return redirect()->route('landing.footer.index')->with('success', __('Footer sub menu created successfully.'));
    }

    public function footerSubMenuEdit($id)
    {
        $footerPage     = FooterSetting::find($id);
        $pages          = PageSetting::pluck('title', 'id');
        $footer         = FooterSetting::where('parent_id', 0)->pluck('menu', 'id');
        return view('landing-page.footer.edit-sub-menu', compact('pages', 'footerPage', 'footer'));
    }

    public function footerSubMenuUpdate(Request $request, $id)
    {
        $pages                      = PageSetting::find($request->page_id);
        $footerSubMenu              = FooterSetting::find($id);
        $footerSubMenu->menu        = $pages->title;
        $footerSubMenu->page_id     = $request->page_id;
        $footerSubMenu->parent_id   = $request->parent_id;
        $footerSubMenu->save();
        return redirect()->route('landing.footer.index')->with('success', __('Footer sub menu updated successfully.'));
    }

    public function footerSubMenuDelete($id)
    {
        $footerSubMenu = FooterSetting::find($id);
        $footerSubMenu->delete();
        return redirect()->back()->with('success', __('Footer sub menu deleted successfully.'));
    }

    public function testimonialSetting()
    {
        return view('landing-page.testimonial-setting');
    }

    public function testimonialSettingStore(Request $request)
    {
        request()->validate([
            'testimonial_name'           => 'required|string|max:191',
            'testimonial_bold_name'      => 'required|string|max:191',
            'testimonial_detail'         => 'required|string',
        ]);
        $testimonialData = [
            'testimonial_setting_enable' => ($request->testimonial_setting_enable && $request->testimonial_setting_enable == 'on') ? 'on' : 'off',
            'testimonial_name'           => $request->testimonial_name,
            'testimonial_bold_name'      => $request->testimonial_bold_name,
            'testimonial_detail'         => $request->testimonial_detail,
        ];
        Self::updateSettings($testimonialData);
        return redirect()->back()->with('success', __('Plan setting updated successfully.'));
    }

    public function planSetting()
    {
        return view('landing-page.plan-setting');
    }

    public function planSettingStore(Request $request)
    {
        request()->validate([
            'plan_name'             => 'required|string|max:191',
            'plan_bold_name'        => 'required|string|max:191',
            'plan_detail'           => 'required|string',
        ]);
        $planData = [
            'plan_setting_enable'   => ($request->plan_setting_enable && $request->plan_setting_enable == 'on') ? 'on' : 'off',
            'plan_name'             => $request->plan_name,
            'plan_bold_name'        => $request->plan_bold_name,
            'plan_detail'           => $request->plan_detail,
        ];
        Self::updateSettings($planData);
        return redirect()->back()->with('success', __('Plan setting updated successfully.'));
    }

    public function loginSetting()
    {
        return view('landing-page.login-setting');
    }

    public function loginSettingStore(Request $request)
    {
        request()->validate([
            'login_image'                => 'image|mimes:png,jpg,jpeg',
            'login_name'                 => 'required|string|max:191',
            'login_detail'               => 'required|string',
        ]);
        $loginData = [
            'login_setting_enable'       => ($request->login_setting_enable && $request->login_setting_enable == 'on') ? 'on' : 'off',
            'login_name'                 => $request->login_name,
            'login_detail'               => $request->login_detail,
        ];
        if ($request->hasFile('login_image')) {
            $imageName                   = 'menusection1.' . $request->login_image->extension();
            $request->login_image->storeAs('landing-page/menu/', $imageName);
            $loginData['login_image']    = 'landing-page/menu/' . $imageName;
        }
        Self::updateSettings($loginData);
        return redirect()->back()->with('success', __('Login setting updated successfully.'));
    }

    public function recaptchaSetting()
    {
        return view('landing-page.recaptcha-setting');
    }

    public function recaptchaSettingStore(Request $request)
    {
        if ($request->contact_us_recaptcha_status == '1' || $request->login_recaptcha_status == '1') {
            request()->validate([
                'recaptcha_key'             => 'required|string|max:191',
                'recaptcha_secret'          => 'required|string|max:191',
            ]);
        }
        $recaptchaSettingData = [
            'contact_us_recaptcha_status'   => ($request->contact_us_recaptcha_status == 'on') ? '1' : '0',
            'login_recaptcha_status'        => ($request->login_recaptcha_status == 'on') ? '1' : '0',
            'recaptcha_key'                 => $request->recaptcha_key,
            'recaptcha_secret'              => $request->recaptcha_secret,
        ];
        Self::updateSettings($recaptchaSettingData);
        return redirect()->back()->with('success', __('Recaptcha setting updated successfully.'));
    }

    public function headerSetting(Request $request)
    {
        $headerSubMenus       = headersetting::get();
        return view('landing-page.header.index', compact('headerSubMenus'));
    }

    public function headerSubMenuCreate()
    {
        $pages                = PageSetting::pluck('title', 'id');
        return view('landing-page.header.create-sub-menu', compact('pages'));
    }

    public function headerSubMenuStore(Request $request)
    {
        $pages                          = PageSetting::find($request->page_id);
        $headerSubMenu                  = new HeaderSetting();
        $headerSubMenu->menu            = $pages->title;
        $headerSubMenu->page_id         = $request->page_id;
        $headerSubMenu->save();
        return redirect()->route('landing.header.index')->with('success', __('Header sub menu created successfully.'));
    }

    public function headerSubMenuEdit($id)
    {
        $headerPage     = HeaderSetting::find($id);
        $pages          = PageSetting::pluck('title', 'id');
        return view('landing-page.header.edit-sub-menu', compact('pages', 'headerPage'));
    }

    public function headerSubMenuUpdate(Request $request, $id)
    {
        $pages                       = PageSetting::find($request->page_id);
        $headerSubMenu               = HeaderSetting::find($id);
        $headerSubMenu->menu         = $pages->title;
        $headerSubMenu->page_id      = $request->page_id;
        $headerSubMenu->save();
        return redirect()->route('landing.header.index')->with('success', __('Header sub menu updated successfully.'));
    }

    public function headerSubMenuDelete($id)
    {
        $headerSubMenu              = HeaderSetting::find($id);
        $headerSubMenu->delete();
        return redirect()->back()->with('success', __('Header sub menu deleted successfully.'));
    }

    public function pageBackground()
    {
        return view('landing-page.background-image');
    }

    public function pageBackgroundStore(Request $request)
    {
        request()->validate([
            'background_image' => 'image|mimes:png,jpg,jpeg',
        ]);
        if ($request->hasFile('background_image')) {
            $imageName                                  = 'background.' . $request->background_image->extension();
            $request->background_image->storeAs('landing-page/', $imageName);
            $backgroundImageData['background_image']    = 'landing-page/' . $imageName;
        }
        Self::updateSettings($backgroundImageData);
        return redirect()->back()->with('success', __('Background setting updated successfully.'));
    }

    public function pagesView($slug)
    {
        $lang           = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        $pageFooter     = FooterSetting::where('slug', $slug)->first();
        $pageSetting    = PageSetting::find($pageFooter->page_id);
        return view('landing-page.footer.pagesView', compact('pageFooter', 'lang', 'pageSetting'));
    }
}
