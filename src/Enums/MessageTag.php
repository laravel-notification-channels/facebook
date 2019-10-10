<?php

namespace NotificationChannels\Facebook\Enums;

/**
 * Class MessageTag.
 */
class MessageTag
{
    /**
     * Send the user reminders or updates for an event they have registered for (e.g., RSVP'ed, purchased tickets).
     * This tag may be used for upcoming events and events in progress.
     *
     * ALLOWED:
     * - Reminder of upcoming classes, appointments, or events that the user has scheduled.
     * - Confirmation of user's reservation or attendance to an accepted event or appointment.
     * - Notification of user's transportation or trip scheduled, such as arrival, cancellation, baggage delay, or
     * other status changes.
     *
     * DISALLOWED:
     * - Promotional content, including but not limited to deals, offers, coupons, and discounts.
     * - Content related to an event the user has not signed up for (e.g., reminders to purchase event tickets,
     * cross-sell of other events, tour schedules, etc).
     * - Messages related to past events.
     * - Prompts to any survey, poll, or reviews.
     */
    public const CONFIRMED_EVENT_UPDATE = 'CONFIRMED_EVENT_UPDATE';

    /**
     * Notify the user of an update on a recent purchase.
     *
     * ALLOWED:
     * - Confirmation of transaction, such as invoices or receipts.
     * - Notifications of shipment status, such as product in-transit, shipped, delivered, or delayed.
     * - Changes related to an order that the user placed, such credit card has declined, backorder items, or other
     * order updates that require user action.
     *
     * DISALLOWED:
     * - Promotional content, including but not limited to deals, promotions, coupons, and discounts.
     * - Messages that cross-sell or upsell products or services.
     * - Prompts to any survey, poll, or reviews.
     */
    public const POST_PURCHASE_UPDATE = 'POST_PURCHASE_UPDATE';

    /**
     * Notify the user of a non-recurring change to their application or account.
     *
     * ALLOWED:
     * - A change in application status (e.g., credit card, job).
     * - Notification of suspicious activity, such as fraud alerts.
     *
     * DISALLOWED:
     * - Promotional content, including but not limited to deals, promotions, coupons, and discounts.
     * - Recurring content (e.g., statement is ready, bill is due, new job listings).
     * - Prompts to any survey, poll, or reviews.
     */
    public const ACCOUNT_UPDATE = 'ACCOUNT_UPDATE';

    /**
     * Allows human agents to respond to user inquiries. Messages can be sent within 7 days after a user message.
     *
     * ALLOWED:
     * - Human agent support for issues that cannot be resolved within the standard messaging window (e.g., business is
     * closed for the weekend, issue requires >24 hours to resolve).
     *
     * DISALLOWED:
     * - Automated messages.
     * - Content unrelated to user inquiry.
     */
    public const HUMAN_AGENT = 'HUMAN_AGENT';
}
