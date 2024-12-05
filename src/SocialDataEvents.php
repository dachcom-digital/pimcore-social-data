<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

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
