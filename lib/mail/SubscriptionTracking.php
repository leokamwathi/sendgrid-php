<?php namespace SendGrid\Mail;

class SubscriptionTracking implements \JsonSerializable
{
    private $enable;
    private $text;
    private $html;
    private $substitution_tag;

    public function __construct($enable=null, $text=null, $html=null, $substitution_tag=null)
    {
        if(isset($enable)) $this->setEnable($enable);
        if(isset($text)) $this->setText($text);
        if(isset($html)) $this->setHtml($html);
        if(isset($substitution_tag)) $this->setSubstitutionTag($substitution_tag);
    }

    public function setEnable($enable)
    {
        $this->enable = $enable;
    }

    public function getEnable()
    {
        return $this->enable;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setHtml($html)
    {
        $this->html = $html;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function setSubstitutionTag($substitution_tag)
    {
        $this->substitution_tag = $substitution_tag;
    }

    public function getSubstitutionTag()
    {
        return $this->substitution_tag;
    }

    public function jsonSerialize()
    {
        return array_filter(
            [
                'enable'           => $this->getEnable(),
                'text'             => $this->getText(),
                'html'             => $this->getHtml(),
                'substitution_tag' => $this->getSubstitutionTag()
            ],
            function ($value) {
                return $value !== null;
            }
        ) ?: null;
    }
}
