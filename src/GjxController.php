<?php

namespace SkinApiExtension;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use App\Models\Texture;
use Blessing\Minecraft;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Storage;

class GjxController extends BaseController
{
    public function getUidBySuffix(Request $request)
    {
        $suffix = $request->input('suffix');
        
        if (empty($suffix)) {
            return response()->json(['error' => '邮箱后缀不能为空'], 400);
        }
        
        $users = User::where('email', 'like', '%' . $suffix)->get();
        $userIds = [];
        
        foreach ($users as $user) {
            $userIds[] = $user->uid;
        }
        
        return response()->json([
            'suffix' => $suffix,
            'count' => count($userIds),
            'user_ids' => $userIds
        ]);
    }
    
    public function getAvatarByUid(Request $request, Minecraft $minecraft)
    {
        $uid = $request->input('uid');
        
        if (empty($uid) || !is_numeric($uid)) {
            return response()->json(['error' => '用户ID必须是数字'], 400);
        }
        
        $uid = (int) $uid;
        $user = User::find($uid);   
        
        if (!$user) {
            return response()->json(['error' => '用户不存在'], 404);
        }
        
        $texture = null;
        if ($user->avatar != 0) {
            $texture = Texture::find($user->avatar);
        }
        
        $disk = Storage::disk('textures');
        
        if (!($texture instanceof Texture) || !$texture->hash || !$disk->exists($texture->hash)) {
            $defaultAvatarPath = resource_path('misc/textures/avatar2d.png');
            if (file_exists($defaultAvatarPath)) {
                return Image::make($defaultAvatarPath)
                    ->response('png', 100);
            } else {
                return response()->json(['error' => '未找到默认头像'], 404);
            }
        }
        
        $file = $disk->get($texture->hash);
        $image = $minecraft->render2dAvatar($file, 25);
        
        return Image::make($image)
            ->response('png', 100);
    }
}
