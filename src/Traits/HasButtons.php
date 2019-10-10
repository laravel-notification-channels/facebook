<?php

namespace NotificationChannels\Facebook\Traits;

use NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage;

/**
 * Trait HasButtons.
 */
trait HasButtons
{
    /** @var array Call to Action Buttons */
    protected $buttons = [];

    /**
     * Add up to 3 call to action buttons.
     *
     * @param  array  $buttons
     *
     * @throws CouldNotCreateMessage
     * @return $this
     */
    public function buttons(array $buttons = []): self
    {
        if (count($buttons) > 3) {
            throw CouldNotCreateMessage::messageButtonsLimitExceeded();
        }

        $this->buttons = $buttons;

        return $this;
    }
}
