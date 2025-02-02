<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties;

/**
 * @link https://wiki.php.net/rfc/dnf_types
 * Changing this file may create some BC change.
 */
class NotSupportedDNFTypes
{
    // Accepts an object that implements both A and B,
    // OR an object that implements D.
    private (A&B)|D $CandBorD;

    // Accepts an object that implements C,
    // OR a child of X that also implements D,
    // OR null.
    private C|(X&D)|null $CorXandDorNULL;

    // Accepts an object that implements all three of A, B, and D,
    // OR an int,
    // OR null.
    private (A&B&D)|int|null $AandBandCorINTorNULL;

    // Accepts an object that implements both A and B,
    // OR an object that implements both A and D.
    private (A&B)|(A&D ) $AandBorAandD;

    // Accepts an object that implements A and B,
    // OR an object that implements both B and D,
    // OR a child of W that also implements B,
    // OR null.
    private A|(B&D)|(B&W)|null $AorBandDorBandWorNULL;

    public function __construct(
        private (A&B)|D $promotedCandBorD,
        private C|(X&D)|null $promotedCorXandDorNULL,
        private (A&B&D)|int|null $promotedAandBandCorINTorNULL,
        private (A&B)|(A&D ) $promotedAandBorAandD,
        private A|(B&D)|(B&W)|null $promotedAorBandDorBandWorNULL,
    ) {
    }
}

interface A
{
}
interface B
{
}
interface C extends A
{
}
interface D
{
}

class W implements A
{
}
class X implements B
{
}
class Y implements A, B
{
}
class Z extends Y implements C
{
}
