<?php

namespace Domain\JobPosts\Entities;

enum JobRemoteStatus
{
    case Remote;
    case Office;
    case Hybrid;
    case Unknown;
}
