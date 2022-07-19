<?php get_header(); ?>
<style>
    #content {
        margin-top: 50px;
    }
    h1 {
        color: #ff671a;
        font-family: 'Nunito', sans-serif;
        font-size: 60px;
        font-weight: 300;
        line-height: 1;
        margin-bottom: 20px;
    }
    h1 b {
        display: block;
        color: #2f425b;
        font-size: 40px;
    }
    #content p {
        font-size: 20px;
        color: #2f425b;
    }
    input.search-field {
        padding: 8px 20px;
        border: solid 1px #e2e2e2;
    }
    input.search-submit {
        background: #ff671a;
        border: none;
        color: #fff;
        padding: 10px 50px;
    }
    @media (max-width: 600px) {
      label {
        display: flex;
        flex-flow: column;
      }
        #content p {
            font-size: 16px;
        }
      input.search-field {
            margin: 5px 0px;
        }
        input.search-submit {
            width: 100%;
        }
    }
</style>

<main id="content" role="main">
    <article id="post-0" class="post not-found max-content text-center">
        <h1 class="entry-title" itemprop="name">
            404
            <b>Lo sentimos</b>
        </h1>
        <div class="entry-content" itemprop="mainContentOfPage">
            <p>No encontramos la página que estás buscando.</p>
           
        </div>
    </article>
</main>
<?php get_footer(); ?>