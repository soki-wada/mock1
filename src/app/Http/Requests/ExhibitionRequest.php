<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png',
            'categories' => 'required',
            'condition_id' => 'required',
            'name' => 'required',
            'description' => 'required|max:255',
            'price' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'image.required' => '商品画像を選択してください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'categories.required' => 'カテゴリーを選択してください',
            'condition_id.required' => '商品の状態を選択してください',
            'name.required' => '商品名を入力
            してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以下で入力してください',
            'price.required' => '商品価格を入力してください'
        ];
    }
}
