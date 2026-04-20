<?php

namespace SkinApiExtension\Middlewares;

use Closure;
use Illuminate\Http\Request;

class TokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->input('token');
        $validTokens = $this->getValidTokens();
        
        if (empty($token) || !in_array($token, $validTokens)) {
            return response()->json(['error' => '无效的token'], 401);
        }
        
        return $next($request);
    }
    
    protected function getValidTokens()
    {
        // 从环境变量获取token
        $envTokens = getenv('SKIN_API_TOKENS');
        if ($envTokens) {
            return explode(',', $envTokens);
        }
    
    }
}