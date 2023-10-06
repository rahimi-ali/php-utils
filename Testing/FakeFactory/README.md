# Fake Factory
This is a simple class that helps with creation of fake objects, array, string or really anything. It offers some of the features that Eloquent Factories provide such as the ability to use dot syntax to access nested definition or the ability to use any callable in your definitions but does not restrict itself to Eloquent models. Also, its PhpStan and IDE safe!

## Usage

### Definition and Make
The main reason this class exists is because Eloquent Factories only work with Eloquent models but I needed something I could use for Doctrine entities or other classes so unlike Eloquent Factories there are 2 required methods that you should implement to get that flexibility:

```php
protected function definition(): array
{
    return [
        'foo' => random_int(1, 100),
        'bar' => 'Whatever'
    ];
}
```

And:

```php
protected function make(array $definition): mixed
{
    // at this point $definition has been materialized, meaning all closures has been called and they have produced values you can use to create your fake object or whatever

    return new FooBar(foo: $definition['foo'], bar: explode($definition['bar']));
}
```


### Override

Lets say this is your definition:

```php
return [
    'foo' => [
        'bar' => random_int(1, 100);
    ],
    'baz' => 'test',
];
```

If you need to override that definition you can use dot syntax like this:

```php
SomeFactory::new()->override(['foo.bar' => 12, 'baz' => fn($currentState) => $currentState['baz'] . 'modified'])->makeOne();

$result = [
    'foo' => [
        'bar' => 12
    ],
    'baz' => 'testmodified',
];
```

Note: callables are evaluated only when makeOne or MakeMany is called therefore, if you had a callable in your definition or previous override what you will get in the `$currentState` will be the callable and you would have to call it manually if you want to do anything with it.

### Adding Faker

```php
abstract class FakeFactoryWithFaker extends FakeFactory
{
    protected \Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fa_IR');
    }
}
```

## Example
```php
class SomeTestCase extends TestCase
{
    #[Test]
    public function itWorks(): void
    {
        // The created user has roles User and Admin
        $user = UserFactory::new()->override(['roles' => fn($currentState) => [...$currentState['roles'], Role::Admin]])->makeOne();

        // OFC, a sane person would probably do this for this particular example if it was this simple
        $user = UserFactory::new()->override(['roles' => [Role::User, Role::Admin]])->makeOne();
    }
}

/** @extends FakeFactory<User> */
class UserFakeFactory extends FakeFactoryWithFaker
{
    protected function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'roles' => [Role::User],
        ];
    }

    protected function make(array $definition): User
    {
        $user = new User();
        $user->setName($definition['name']);
        foreach($definition['roles'] as $role) {
            $user->addRole($role);
        }
        return $user;
    }
}

#[Document]
class User
{
    #[Field(type: 'string')]
    private string $name;

    #[EmbedMany(targetDocument: UserRole::class)]
    private ArrayCollection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function addRole(Role $role): self
    {
        $this->roles->add(new UserRole($role));
    }
}

#[EmbeddedDocument]
class UserRole
{
    #[Field(type: 'string', enumType: Role::class)]
    private Role $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }
}

enum Role: string
{
    case User = 'user';
    case Admin = 'admin';
}
```

