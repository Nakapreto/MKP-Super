<?php

require_once SUPER_LINKS_HELPERS_PATH . '/vendor/autoload.php';
use html_changer\HtmlChanger;

class TextHelper {

    /**
     * @var string
     */
    private $startText;

    /**
     * @var string
     */
    private $text;

    /**
     * @var array
     */
    private $counts = [];

    /**
     * @var array
     */
    private $links = [];

    /**
     * TextConverter constructor.
     * @param $text
     */
    public function __construct($text)
    {
        $this->startText = $text;
        $this->text = $text;
    }

    /**
     * Add links to html
     *
     * @param array $links
     * @return $this
     */
    public function addLinks(array $links)
    {
        $search = [];
        /**
         * @var Link $link
         */
        foreach ($links as $link) {
            if($link->num === 0) {
                continue;
            }
            $superLink = new SuperLinksAutomaticLinksModel();
            $superLink->loadDataByID($link->id);
            $link = $superLink;
            foreach($link->getKeywords() as $keyword) {
                $options = [
                    'caseInsensitive' => true, 
                    'wordBoundary' => !$link->getAttribute('partly_match'),
                    'value' => $link,
                    'group' => $link->getAttribute('id'),
                    'maxCount' => $link->getAttribute('num'),
                ];
                $trimmedKeyword = trim($keyword);
                if(empty($trimmedKeyword)) {
                    // do not add empty keywords
                    continue;
                }
                // first add original keyword
                $search[$trimmedKeyword] = $options;

                // escape keyword
                $escapedKeyword = esc_html($trimmedKeyword);
                $search[$escapedKeyword] = $options;

                // "detexturize" keyword
                $escapedKeyword2 = preg_replace('/’/', "'", $trimmedKeyword);
                // texturize keyword with wordpress function
                $escapedKeyword2 = wptexturize($escapedKeyword2);
                $search[$escapedKeyword2] = $options;

                $escapedKeyword3 = $trimmedKeyword;
                $search[$escapedKeyword3] = $options;
            }
        }
        $htmlChanger = new HtmlChanger($this->text, [
            'search' => $search,
            'ignore' => array_merge([
                    'a', // links
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', // headlines
                ], 
                []
            )
        ]);

        $htmlChanger->replace(function($text, SuperLinksAutomaticLinksModel $link) {
            return $link->getReplaceString($text);
        });

        $this->text = $htmlChanger->html();

        // fluent interface
        return $this;
    }

    /**
     * Get transformed text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text The text that should be replaced
     * @param Link   $link
     * @return string
     */
    private function replaceText($text, SuperLinksAutomaticLinksModel $link)
    {
        return $link->getReplaceString($text);
    }

}
