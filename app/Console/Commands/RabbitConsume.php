<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitConsume extends Command
{
    /**
     * @var string
     */
    protected $signature = 'some-queue:consume';

    /**
     * @var string
     */
    protected $description = 'Consume and send notifications from the queue';

    public function handle(): int
    {
        $hostConfig = config('queue.connections.rabbitmq.hosts.0');
        $connection = new AMQPStreamConnection($hostConfig['host'], $hostConfig['port'], $hostConfig['user'], $hostConfig['password']);
        $channel = $connection->channel();

        $channel->queue_declare('notifications', false, true, false, false);

        $this->line(' [*] Waiting for messages. To exit press CTRL+C');

        $channel->basic_qos(0, 1, false);

        $channel->basic_consume('notifications', callback: $this->callback(...));

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();

        return self::SUCCESS;
    }

    private function callback(AMQPMessage $msg): void
    {
        $this->line(' [x] Received');
        $data = json_decode($msg->getBody(), true);
        dump($data);

        dump('Delivery Tag :', $msg->getDeliveryTag());

        $msg->getChannel()->basic_ack($msg->getDeliveryTag());
    }
}
