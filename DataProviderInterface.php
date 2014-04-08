<?php

namespace Anh\FeedBundle;

interface DataProviderInterface
{
    public function getData($feed, array $parameters);

    public function getType();

    public function getUpdatedAt(array $parameters);
}