<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'username' => 'required',
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required',
            'image' => 'image|mimes:jpeg,png|required_without:old_image',
            'old_image' => 'nullable|string|filled'  //input.fileに登録済みの画像を画像ファイルとして受け渡さないため
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'ユーザー名を入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください。',
            'address.required' => '住所を入力してください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.required_without' => 'プロフィール画像を選択してください'
        ];
    }
}
