<?php

namespace App\Http\Requests;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = User::find(1);
        if (!$user) {
            return false;
        }
        Auth::login($user);
        return $user->contacts()->where('contact_id', $this->contact_id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:5000',
            'contact_id' => 'required|exists:contacts,id',
            'channel_id' => 'required|exists:channels,id',
        ];
    }
}
