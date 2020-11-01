import * as React from 'react';
import ContentBox from '@/components/elements/ContentBox';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import { breakpoint } from '@/theme';
import styled from 'styled-components/macro';
import { faCoins } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Link } from 'react-router-dom';

const Container = styled.div`
    ${tw`flex flex-wrap my-10`};

    & > div {
        ${tw`w-full`};

        ${breakpoint('md')`
            width: calc(50% - 1rem);
        `}

        ${breakpoint('xl')`
            ${tw`w-auto flex-1`};
        `}
    }
`;

export default () => {
    return (
        <PageContentBlock title={'Coins Overview'}>
            <Container>
                <ContentBox css={tw`w-full`} title={'Netric Coins'}>
                    <h3 css={tw`text-2xl font-bold mb-3 leading-7`}>What is Netric Coins?</h3>
                    <p>
                        {'Netric Coins'} is our own currency that you can use to exchange into Nodes resources.
                        You can earn some Netric Coins by referring friends, keeping your browser open on our website, or following certain earning methods mentioned here.
                        {'It\'s'} always free to earn {'Netric Coins'} and we {'won\'t'} charge any cost for it.
                    </p>
                </ContentBox>
                <ContentBox css={tw`xl:ml-8 mt-8 xl:mt-0`} title={'Coins Earned'}>
                    <div css={tw`flex items-start px-4 py-6 w-full`}>
                        <span css={tw`w-12 h-12 rounded-full object-cover mr-4 shadow`}>
                            <span css={tw`flex items-center justify-center h-full`}>
                                <FontAwesomeIcon icon={faCoins} />
                            </span>
                        </span>
                        <div css={tw`w-full`}>
                            <div css={tw`flex items-center justify-between mb-3`}>
                                <h2
                                    css={tw`text-lg font-semibold text-white -mt-1`}
                                >
                                    Total Nitros:
                                </h2>
                                <span css={tw`text-green-500 font-black`}>0 🚀</span>
                            </div>
                            <div css={tw`flex items-center justify-between`}>
                                <p css={tw`text-white`}>Last earned:</p>
                                <p css={tw`text-white`}>
                                    12 Sep 2012
                                </p>
                            </div>
                            <div css={tw`mt-4 flex items-center justify-between`}>
                                <p css={tw`text-white`}>Want to get more nitros?</p>
                                <div css={tw`flex text-white text-sm`}>
                                    <Link
                                        to="/coins/earn"
                                        css={tw`bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded`}
                                    >
                                        Earn more
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </ContentBox>
            </Container>
        </PageContentBlock>
    );
};
