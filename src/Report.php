<?php

declare(strict_types=1);

namespace Exan\StabilityBot;

use ByteUnits\Metric;
use Carbon\Carbon;
use DateInterval;
use Exan\Fenrir\Rest\Helpers\Channel\EmbedBuilder;
use Exan\Fenrir\Rest\Helpers\Channel\MessageBuilder;

class Report
{
    private int $memory;
    private DateInterval $uptime;
    private string $phpVersion;

    public function __construct(
        private readonly string $libraryVersion,
        Carbon $startTime,
    ) {
        $this->memory = memory_get_usage();
        $this->uptime = (new Carbon())->diff($startTime);
        $this->phpVersion = phpversion();
    }

    public function toMessageBuilder(): MessageBuilder
    {
        $embed = new EmbedBuilder();

        foreach ($this->getEmbedFields() as $name => $value) {
            $embed->addField($name, $value);
        }

        return (new MessageBuilder())
            ->addEmbed($embed);
    }

    /**
     * @return array<string, string>
     */
    private function getEmbedFields(): array
    {
        return [
            'Fenrir version' => $this->libraryVersion,
            'PHP version' => $this->phpVersion,
            'Uptime' => $this->uptime->format('%d days, %H:%I:%S'),
            'Memory usage' => Metric::bytes($this->memory)->format(),
        ];
    }
}