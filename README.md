HshnSerializerExtraBundle
=========================
[![Build Status](https://travis-ci.org/hshn/HshnSerializerExtraBundle.svg?branch=master)](https://travis-ci.org/hshn/HshnSerializerExtraBundle) [![Latest Stable Version](https://poser.pugx.org/hshn/serializer-extra-bundle/v/stable.svg)](https://packagist.org/packages/hshn/serializer-extra-bundle) [![Total Downloads](https://poser.pugx.org/hshn/serializer-extra-bundle/downloads.svg)](https://packagist.org/packages/hshn/serializer-extra-bundle) [![Latest Unstable Version](https://poser.pugx.org/hshn/serializer-extra-bundle/v/unstable.svg)](https://packagist.org/packages/hshn/serializer-extra-bundle) [![License](https://poser.pugx.org/hshn/serializer-extra-bundle/license.svg)](https://packagist.org/packages/hshn/serializer-extra-bundle)

This bundle provides some extra features for serialization.

### Exporting authorities of objects

```yaml
# app/config.yml
hshn_serializer_extra:
    authority:
        classes:
            AcmeBundle\Entity\Blog:
                attributes: OWNER
```

```php
/** @var $serializer JMS\Serializer\Serializer */
$json = $serializer->serialize($blog, 'json');
```

The access authorities provided by `AuthorizationCheckerInterface::isGranted()` will be exported to the attribute '_authority' when an object was serialized.

```json
{
    "key": "value",
    "_authority": {
        "OWNER": true
    }
}
```

#### Overriding the attribute name

```yaml
# app/config.yml
hshn_serializer_extra:
    authority:
        export_to: "my_authority"
```

```json
{
    "key": "value",
    "my_authority": {
        "OWNER": true
    }
}
```

#### Restrict exporting the authorities by depth

```yaml
# app/config.yml
hshn_serializer_extra:
    authority:
        classes:
            AcmeBundle\Entity\Blog:
                attributes: [OWNER]
                max_depth: 0 # default -1 (unlimited)
```

```php
class Blog
{
}

class User
{
    /**
     * @var Blog
     */
    private $blog;
}

$serializer->serialize($blog, 'json'); // will export the blog authorities (depth 0)
$serializer->serialize($user, 'json'); // will NOT export the blog authorities (depth 1)
```

### Export files as URLs

> This feature require [VichUploaderBundle](https://github.com/dustin10/VichUploaderBundle)

```yaml
# app/config.yml
hshn_serializer_extra:
    vich_uploader:
        classes:
            AcmeBundle\Entity\Blog:
                files:
                    - { property: picture }
                    - { property: picture, export_to: image }
```

```php
/** @var $serializer JMS\Serializer\Serializer */
$json = $serializer->serialize($blog, 'json');
```

Generated URLs will be exported when serializing an object.

```json
{
    "picture": "/url/to/picture",
    "image": "/url/to/picture"
}
```

### Export images as URLs

> This feature require [LiipImagineBundle](https://github.com/liip/LiipImagineBundle)

Adding a filter name specification to a file configuration.

```yaml
# app/config.yml
hshn_serializer_extra:
    vich_uploader:
        classes:
            AcmeBundle\Entity\Blog:
                files:
                    - { property: picture }
                    - { property: picture, export_to: image, filter: thumbnail }
```

```json
{
    "picture": "/url/to/picture",
    "image": "/url/to/thumbnail/picture"
}
```
