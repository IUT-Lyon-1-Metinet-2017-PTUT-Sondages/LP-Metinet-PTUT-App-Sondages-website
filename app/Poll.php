<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'title', 'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Force la récupération des Answer[...] des Question, car la méthode $poll->toArray()
     * (avec les ->with([...]) ne pouvait pas récupérer les Answer[...] des Question, à cause
     * de la relation "dynamique" entre la table 'questions', et l'une des tables 'answers_...'.
     *
     * @return array
     */
    public function toArrayWithAllRelations()
    {
        $poll = $this->with(['user', 'pages', 'pages.questions'])->first();
        $arr = $poll->getAttributes();
        $arr['user'] = $poll->user->toArray();

        /**
         * @var integer $i
         * @var Page $page
         */
        foreach ($poll->pages as $i => $page) {
            $arr['pages'][$i] = $page->toArray();
            /**
             * @var integer $j
             * @var Question $question
             */
            foreach ($page->questions as $j => $question) {
                $arr['pages'][$i]['questions'][$j] = $question->toArray();
                /**
                 * @var integer $k
                 * @var AnswerInterface $answer
                 */
                foreach ($question->answers as $k => $answer) {
                    $arr['pages'][$i]['questions'][$j]['answers'][$k] = $answer->toArray();
                }
            }
        }

        return $arr;
    }

    /**
     * Pareil que Poll::toArrayWithAllRelations(), mais retourne du JSON.
     * @param int $options
     * @return string
     */
    public function toJsonWithAllRelations($options = 0)
    {
        return json_encode($this->toArrayWithAllRelations(), $options);
    }
}
