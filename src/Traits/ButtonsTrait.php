<?php

namespace NotificationChannels\Facebook\Traits;

use NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage;

trait ButtonsTrait
{

    /** @var array Call to Action Buttons */
    public $buttons = [];

    /**
     * Add up to 3 call to action buttons.
     *
     * @param array $buttons
     *
     * @return $this
     * @throws CouldNotCreateMessage
     */
    public function buttons(array $buttons = [])
    {
        if (count($buttons) > 3) {
            throw CouldNotCreateMessage::messageButtonsLimitExceeded();
        }
        $this->buttons = $buttons;
        return $this;
    }

}