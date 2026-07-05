<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:191', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'user_type' => ['required', 'in:student,graduate,academic'],
            'student_no' => ['nullable', 'string', 'max:30'],
            'graduation_year' => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:' . date('Y')],
            'privacy_notice_approved' => ['accepted'],
            'legal_texts_reviewed' => ['accepted'],
        ];
    }

   public function messages(): array
{
    return [
        'name.required' => 'Ad alanı zorunludur.',
        'name.max' => 'Ad en fazla 100 karakter olabilir.',

        'surname.required' => 'Soyad alanı zorunludur.',
        'surname.max' => 'Soyad en fazla 100 karakter olabilir.',

        'email.required' => 'E-posta alanı zorunludur.',
        'email.email' => 'Geçerli bir e-posta adresi gir.',
        'email.max' => 'E-posta en fazla 191 karakter olabilir.',
        'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',

        'password.required' => 'Şifre alanı zorunludur.',
        'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
        'password.min' => 'Şifre en az 8 karakter olmalıdır.',

        'user_type.required' => 'Kullanıcı tipi seçmelisin.',
        'user_type.in' => 'Geçersiz kullanıcı tipi seçildi.',

        'student_no.max' => 'Öğrenci numarası en fazla 30 karakter olabilir.',

        'graduation_year.integer' => 'Mezuniyet yılı sayı olmalıdır.',
        'graduation_year.digits' => 'Mezuniyet yılı 4 haneli olmalıdır.',
        'graduation_year.min' => 'Mezuniyet yılı 1900 veya daha büyük olmalıdır.',
        'graduation_year.max' => 'Mezuniyet yılı geçerli bir değer olmalıdır.',

        'privacy_notice_approved.accepted' => 'Devam etmek için Aydınlatma Metni onayını işaretlemelisin.',
        'legal_texts_reviewed.accepted' => 'Devam etmek için yasal metinleri incelediğini onaylamalısın.',
    ];
}
}