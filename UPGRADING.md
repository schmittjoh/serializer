This document details the changes that you need to make to your code
when upgrading from one version to another.

Upgrading From 0.9 to 1.0
=========================

- Custom Handlers

    The interfaces ``SerializationHandlerInterface``, and ``DeserializationHandlerInterface``
    have been removed. Instead, you can now use either an event listener, or the new handler
    concept. As a general rule, if your handler was registered for a specific type, you
    would use the new handler system, if you instead were handling an arbitrary number of
    possibly unknown types, you would use the event system.

    Please see the documentation for how to set-uup one of these.

- Configuration

    Most of the configuration under ``jms_serializer.handlers`` is gone. The order is not
    important anymore as a handler can only be registered for one specific type.

    You can still configure the built-in ``datetime`` handler though:

    ```
    jms_serializer:
        handlers:
            datetime:
                default_format: DateTime::ISO8601
                default_timzone: UTC
    ```

    This is not necessary anymore though as you can now specify the format each time when
    you use a DateTime by using the @Type annotation:

    ```
    /** @Type("DateTime<'Y-m-d', 'UTC'>") */
    private $createdAt;
    ```
