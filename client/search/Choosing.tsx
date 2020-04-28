import React from 'react';
import { ofType, useDelta, RootJunction, toJunction, Delta } from 'brookjs';
import Kefir from 'kefir';
import { RootAction } from '../util';
import { searchBlobSelected, searchRepoSelected } from './actions';
import { View } from './View';
import { reducer, initialState, State, Collection } from './state';
import { searchDelta } from './delta';
import { useGlobals, usePrismConfig } from './context';
import { isLoading, hasError, hasSnippets } from './selectors';

const rootDelta: Delta<RootAction, State> = (action$, state$) => {
  const search$ = searchDelta(
    action$,
    state$.map(state => ({
      root: state.globals.root,
      nonce: state.globals.nonce,
      term: state.term,
      collection: state.collection,
    })),
  );

  return Kefir.merge([search$]);
};

const Choosing: React.FC<{ collection: Collection }> = ({ collection }) => {
  const globals = useGlobals();
  const prism = usePrismConfig();
  const { state, root$ } = useDelta(
    reducer,
    { ...initialState, collection, globals },
    rootDelta,
  );
  return (
    <RootJunction root$={root$}>
      <View
        searchLabel={
          collection === 'blobs' ? 'Search snippets' : 'Search repos'
        }
        placeholderLabel={
          collection === 'blobs' ? 'placeholder.js' : 'Placeholder Repo Name'
        }
        term={state.term}
        isLoading={isLoading(state)}
        error={hasError(state) ? state.error : null}
        results={
          hasSnippets(state)
            ? state.results.collection === 'blobs'
              ? state.results.response.map(blob => ({
                  id: blob.ID,
                  label: blob.filename,
                  render: {
                    blob: {
                      filename: blob.filename,
                      code: blob.code,
                      language: blob.language.slug,
                    },
                    prism,
                  },
                }))
              : state.results.response.map(repo => ({
                  id: repo.ID,
                  label: repo.description,
                  prism,
                }))
            : null
        }
      />
    </RootJunction>
  );
};

export default toJunction(
  {},
  action$ => action$.thru(ofType(searchBlobSelected, searchRepoSelected)),
  // @TODO(mAAdhaTTah) fix cast!
)(Choosing as any) as React.FC<{ collection: Collection }>;
