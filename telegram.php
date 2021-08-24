<?php

/**
 * Serpentine / Telegram IO
 * Copyright (C) 2021  Nikita Podvirnyy

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * GitHub: https://github.com/KRypt0nn
 * VK:     https://vk.com/technomindlp
 */



namespace Serpentine\IO;

use Serpentine\{
    IO,
    Message
};

class Telegram implements IO
{
    protected string $token;
    protected ?int $lastUpdate = null;

    public function __construct (string $token)
    {
        $this->token = $token;
    }

    public function getUpdates (): array
    {
        $updates = $this->lastUpdate === null ?
            $this->query ('getUpdates') :
            $this->query ('getUpdates', [
                'offset' => $this->lastUpdate + 1
            ]);

        if (($updates['ok'] ?? false) != true)
            return [];

        $response = [];

        foreach ($updates['result'] as $update)
        {
            $response[] = Message::new ($update['message']['text'], $update['message']['from']['id']);

            $this->lastUpdate = $update['update_id'];
        }

        return $response;
    }

    public function sendMessage (int $receiver, Message $message): self
    {
        $this->query ('sendMessage', [
            'chat_id' => $receiver,
            'text'    => $message->getText ()
        ]);

        return $this;
    }

    public function query (string $method, array $params = []): ?array
    {
        $params = array_map (fn ($item) => is_array ($item) ? json_encode ($item) : $item, $params);
        $response = @json_decode (@file_get_contents ('https://api.telegram.org/bot'. $this->token .'/'. $method . (sizeof ($params) > 0 ? '?'. http_build_query ($params) : '')), true) ?: [];

        return is_array ($response) ? $response : null;
    }
}
