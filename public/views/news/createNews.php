<?php
defined('ROOT_PATH') OR exit();

$iterator = $this->userAssociations->getIterator(AssociationsList::$USER_CAN_WRITE_NEWS_ORDER);

$iterator->valid() OR exit("No associations that you can write news for.\nTry joining one, or maybe you just don't have premissions or you need to pay your quotas!"); // if is valid there are associations the user can write news to
?>

<section id="<?php echo !isset($SESSION_['editNews']) ? 'create' : 'edit' ?>">

<header>
<h1><?php echo !isset($SESSION_['editNews']) ? 'Create' : 'Edit' ?> News</h1>
</header>

<?php
/**
 * The corrected user input and the errors that were made on the admni model.
 */
if (isset($_SESSION['news'])) {
    $create = unserialize($_SESSION['news']);
    unset($_SESSION['news']);
} else
    $create = [];

if (isset($_SESSION['news-errors'])) {
    $errors = $_SESSION['news-errors'];
    unset($_SESSION['news-errors']);
} else
    $errors = [];

if (isset($_SESSION['news-created'])) {
    $created = $_SESSION['news-created'];
    unset($_SESSION['news-created']);
} else
    $created = '';

$assocsCanPublish = [];
?>

<form method="post"
    enctype="multipart/form-data">
    <p><label>News title: <input type="text" name="create[title]" value="<?php
        if (isset($create['title']))
            echo htmlspecialchars($create['title'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    ?>" maxlength="80" minlength="1" required size="80" /></label></p>
    <p><label>Article: <textarea autocomplete="on" cols="80" rows="20" minlength="1" placeholder="News article body..." name="create[article]" required><?php
        if (isset($create['article']))
            echo htmlentities($create['article']);
    ?></textarea></label></p>
    <p><label>Image: <input type="file" name="create-image" accept="image/*" required /></label></p>
    <fieldset>
        <legend>Association:</legend>
        <p>
            <label>What association is this news too?:
                <select name="create[association]" value="<?=checkArray($create, 'association')?>">
                <?php
                    $iterator->rewind();
                    while ($iterator->valid()) {
                        $association = $iterator->current();

                        echo '<option value="' . $association->getID() . '"' . (isset($this->parameters[0]) && $this->parameters[0] === $association->nickname ? ' selected' : '') . '>' . $association->name . '</option>';

                        if ($iterator->canPublish($association))
                            $assocsCanPublish[] = $association->getID();

                        $iterator->next();
                    }
                ?>
                </select>
            </label>
        </p>
    </fieldset>
    <p><button>Create</button></p>
</form>

<?php unset($create) ?>

<aside>
<?php if (!empty($created)): ?>
<h3>It Worked!</h3>
<p><?=$created?></p>
<?php endif ?>
<?php unset($created); ?>
<?php if (!empty($errors)): ?>
<h3>Errors Found</h3>
<?php foreach ($errors as $error): ?>
<p><?=$error?></p>
<?php endforeach ?>
<?php endif ?>
<?php unset($errors); ?>
</aside>

<aside>
<h3>How to create a news article</h3>
<p>To write a paragraph there's no p element, you just need to create an empty new line.</p>
<p>The article textarea won't accept HTML5 tags other than <a href="https://html.spec.whatwg.org/multipage/text-level-semantics.html">text-level semantics tags</a>.</p>
<details>
<summary>When to use the text-level semantics tags</summary>
<dl>
    <dt> &lt;a&gt;
    <dd> Hyperlinks
    <dt> &lt;em&gt;
    <dd> Stress emphasis
    <dt> &lt;strong&gt;
    <dd> Importance
    <dt> &lt;small&gt;
    <dd> Side comments
    <dt> &lt;s&gt;
    <dd> Inaccurate text
    <dt> &lt;cite&gt;
    <dd> Titles of works
    <dt> &lt;q&gt;
    <dd> Quotations
    <dt> &lt;dfn&gt;
    <dd> Defining instance
    <dt> &lt;abbr&gt;
    <dd> Abbreviations
    <dt> &lt;ruby&gt;, &lt;rt&gt;, &lt;rp&gt;
    <dd> Ruby annotations
    <dt> &lt;data&gt;
    <dd> Machine-readable equivalent
    <dt> &lt;time&gt;
    <dd> Machine-readable equivalent of date- or time-related data
    <dt> &lt;code&gt;
    <dd> Computer code
    <dt> &lt;var&gt;
    <dd> Variables
    <dt> &lt;samp&gt;
    <dd> Computer output
    <dt> &lt;kbd&gt;
    <dd> User input
    <dt> &lt;sub&gt;
    <dd> Subscripts
    <dt> &lt;sup&gt;
    <dd> Superscripts
    <dt> &lt;i&gt;
    <dd> Alternative voice
    <dt> &lt;b&gt;
    <dd> Keywords
    <dt> &lt;u&gt;
    <dd> Annotations
    <dt> &lt;mark&gt;
    <dd> Highlight
    <dt> &lt;bdi&gt;
    <dd> Text directionality isolation
    <dt> &lt;bdo&gt;
    <dd> Text directionality formatting
    <dt> &lt;span&gt;
    <dd> Other
    <dt> &lt;br&gt;
    <dd> Line break
    <dt> &lt;wbr&gt;
    <dd> Line breaking opportunity
</dl>
</details>
</aside>

</section>
