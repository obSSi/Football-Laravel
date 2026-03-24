<?php

namespace App\Http\Requests;

use App\Models\Fixture;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateFixtureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'match_id' => ['required', 'integer'],
            'maison_id' => ['required', 'integer', 'exists:equipes,id', 'different:exterieur_id'],
            'exterieur_id' => ['required', 'integer', 'exists:equipes,id', 'different:maison_id'],
            'score1' => ['required', 'integer', 'min:0', 'max:50'],
            'score2' => ['required', 'integer', 'min:0', 'max:50'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $fixture = $this->route('fixture');

            if (!$fixture instanceof Fixture) {
                return;
            }

            if ($this->integer('match_id') !== (int) $fixture->id) {
                $validator->errors()->add('match_id', 'Le match cible ne correspond pas.');

                return;
            }

            $maisonId = $this->integer('maison_id');
            $exterieurId = $this->integer('exterieur_id');

            $teamsInChampionnat = \App\Models\Equipe::query()
                ->where('championnat_id', $fixture->championnat_id)
                ->whereIn('id', [$maisonId, $exterieurId])
                ->pluck('id')
                ->all();

            if (count($teamsInChampionnat) !== 2) {
                $validator->errors()->add('maison_id', 'Les equipes maison et exterieur doivent appartenir au championnat du match.');
            }

            $duplicateMatch = Fixture::query()
                ->where('championnat_id', $fixture->championnat_id)
                ->where('equipe1_id', $maisonId)
                ->where('equipe2_id', $exterieurId)
                ->where('id', '!=', $fixture->id)
                ->exists();

            if ($duplicateMatch) {
                $validator->errors()->add('maison_id', 'Cette rencontre maison/exterieur existe deja.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'maison_id.required' => 'L equipe maison est obligatoire.',
            'maison_id.different' => 'Les equipes maison et exterieur doivent etre differentes.',
            'maison_id.exists' => 'L equipe maison est invalide.',
            'exterieur_id.required' => 'L equipe exterieur est obligatoire.',
            'exterieur_id.different' => 'Les equipes maison et exterieur doivent etre differentes.',
            'exterieur_id.exists' => 'L equipe exterieur est invalide.',
            'score1.required' => 'Le score maison est obligatoire.',
            'score1.integer' => 'Le score maison doit etre un entier.',
            'score1.min' => 'Le score maison ne peut pas etre negatif.',
            'score1.max' => 'Le score maison ne peut pas depasser 50.',
            'score2.required' => 'Le score exterieur est obligatoire.',
            'score2.integer' => 'Le score exterieur doit etre un entier.',
            'score2.min' => 'Le score exterieur ne peut pas etre negatif.',
            'score2.max' => 'Le score exterieur ne peut pas depasser 50.',
        ];
    }
}
