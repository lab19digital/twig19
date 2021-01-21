TWIG: https://twig.symfony.com/  
SCSS: https://sass-lang.com/  
BEM: http://getbem.com/introduction/

## Data

The data is set in the `context/data` folder. In this folder you will find 2 folders `pages` and `components`.

In the `pages` folder we set the data for each page, and it will be available in twig like this `page['file-name']`.

In the `components` folder we set the _default_ data for each component and it will be available in twig like this `component['file-name']`.

#### Examples

```twig
{% include 'components/c-example.twig' with component['c-example'] only %}

{% include 'components/c-example.twig' with page.home.example only %}

{% include 'components/c-example.twig' with {
  image: {
    src: 'https://via.placeholder.com/300'
  },
  title: 'Example Component with different data',
  text: 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',
  button: 'Do Something'
} only %}
```

## Markup

#### Example

```twig
{# twig/components/c-example.twig #}

{% from 'mixins.twig' import fa %}

<section class="c-example {{ classes }}">
  {% if image %}
    <img src="{{ image.src }}" alt="{{ image.alt }}">
  {% endif %}

  {% if title %}
    <h1 class="c-example__title">{{ title }}</h1>
  {% endif %}

  {% if text %}
    <p class="c-example__text">{{ text }}</p>
  {% endif %}

  {% if button %}
    <button class="c-example__button">
      {{ button }}
      <span style="display: inline-block; width: 20px;">{{ fa('regular/bell') }}</span>
    </button>
  {% endif %}
</section>
```

## Styles

For the styles we use SCSS and the BEM naming convention and follow a mobile first approach, try to use mostly `@include media-breakpoint-up() {}`.

#### Example

```scss
// scss/components/_c-example.scss

.c-example {
  margin: spacing(20, 0);

  &__title {
    margin: spacing(0, 0, 3);
    font-size: rem(32);
    @include transitions('color', 1s);
  }

  &__text {
    color: pink;
  }

  &__button {
    padding: spacing(2, 5);
    font-size: rem(12);
    background-color: coral;
    @include transitions('background-color');
  }

  &--primary {
    .c-example__title {
      color: purple;
    }

    .c-example__button {
      background-color: aquamarine;
    }
  }

  @include media-breakpoint-up(md) {
    &__title {
      margin-bottom: spacing(5);
      font-size: rem(40);
    }

    &__button {
      padding: spacing(3, 6);
      font-size: $font-size-base;
    }
  }
}
```

## JavaScript

#### Example

```js
// js/components/c-example.js

export const cName = 'c-example'

export default () => {
  $(`.${cName}`).each(function () {
    const c = {}

    c.self = this
    c.$self = $(c.self)
    c.$button = c.$self.find(`.${cName}__button`)

    c.$button.on('click', () => c.$self.toggleClass(`${cName}--primary`))
  })
}
```
