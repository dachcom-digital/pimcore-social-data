<?php

namespace SocialDataBundle;

final class SocialDataEvents
{
    public const SOCIAL_POST_BUILDER_FETCH_CONFIGURE = 'social_data.social_post_builder.fetch_configure';
    public const SOCIAL_POST_BUILDER_FETCH_POST = 'social_data.social_post_builder.fetch_post';

    public const SOCIAL_POST_BUILDER_FILTER_CONFIGURE = 'social_data.social_post_builder.filter_configure';
    public const SOCIAL_POST_BUILDER_FILTER_POST = 'social_data.social_post_builder.filter_post';

    public const SOCIAL_POST_BUILDER_TRANSFORM_CONFIGURE = 'social_data.social_post_builder.transform_configure';
    public const SOCIAL_POST_BUILDER_TRANSFORM_POST = 'social_data.social_post_builder.transform_post';
}
