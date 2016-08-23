<?php

namespace NotificationChannels\Facebook\Traits;

use NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage;

trait HasButtons
{
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

        $this->payload['buttons'] = $buttons;

        return $this;
    }
}
