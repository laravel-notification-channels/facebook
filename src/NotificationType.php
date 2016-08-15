<?php

namespace NotificationChannels\Facebook;

interface NotificationType
{
    /** Emit sound/vibration and a phone notification */
    const REGULAR = 'REGULAR';

    /** Emit a phone notification */
    const SILENT_PUSH = 'SILENT_PUSH';

    /** Not emit */
    const NO_PUSH = 'NO_PUSH';
}
